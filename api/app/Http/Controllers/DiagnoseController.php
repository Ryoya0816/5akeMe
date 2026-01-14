<?php

namespace App\Http\Controllers;

use App\Models\DiagnoseResult;
use App\Services\DiagnoseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DiagnoseController extends Controller
{
    /**
     * 質問セットを返す API
     *
     * POST /api/diagnose/start
     *
     * リクエスト（任意）:
     *   { "seed": 123 }
     *
     * レスポンス:
     * {
     *   "seed": 123,
     *   "questions": [
     *     { "id": "q1", "text": "...", "choices": [...] },
     *     { "id": "q2", "text": "...", "choices": [...] },
     *     { "id": "A?", ... },
     *     { "id": "B?", ... },
     *     { "id": "C?", ... }
     *   ]
     * }
     */
    public function start(Request $request, DiagnoseService $service)
    {
        // JSON / フォーム / クエリ どこに来ても OK にしておく
        $data = $request->validate([
            'seed' => 'nullable|integer',
        ]);

        $seed = $data['seed'] ?? null;

        // サービス側で「固定2 + A/B/C 各1問」の 5問セットを組み立て
        $session = $service->createSession($seed);

        $questions = $session['questions'] ?? [];

        if (!is_array($questions) || count($questions) === 0) {
            // 想定外パターンはログを吐いて 500
            Log::error('[Diagnose] empty questions from createSession', [
                'seed'    => $seed,
                'session' => $session,
            ]);

            return response()->json([
                'message' => '質問の取得に失敗しました。',
            ], 500);
        }

        // 念のため 0,1,2... の連番に揃える
        $questions = array_values($questions);

        return response()->json([
            'seed'      => $session['seed'] ?? $seed,
            'questions' => $questions,
        ]);
    }

    /**
     * 採点 API
     *
     * POST /api/diagnose/score
     *
     * 期待リクエスト:
     * {
     *   "answers": {
     *     "q1": "a",
     *     "q2": "c",
     *     "A1": "b",
     *     ...
     *   },
     *   "seed": 123   // 任意
     * }
     *
     * レスポンス:
     * {
     *   "result_id": "res_xxx",
     *   "result": {
     *     "primary": "sake_dry",
     *     "primary_label": "日本酒・辛口",
     *     "candidates": [...],
     *     "mood": "lively"
     *   }
     * }
     */
    public function score(Request $request, DiagnoseService $service)
    {
        try {
            $data = $request->validate([
                'answers' => 'required|array',
                'seed'    => 'nullable|integer',
            ]);

            $answers = $data['answers'];
            $seed    = $data['seed'] ?? null;

            // サービスで採点
            // 戻り値想定:
            // ['primary' => 'sake_dry', 'candidates' => [...], 'mood' => 'lively', 'scores' => [...]]
            $scored = $service->score($answers, $seed);

            if (empty($scored['primary'])) {
                Log::warning('[Diagnose] primary type is null', [
                    'answers' => $answers,
                    'seed'    => $seed,
                    'scored'  => $scored,
                ]);

                return response()->json([
                    'message' => '診断に失敗しました。',
                ], 422);
            }

            // primary のラベルは candidates から拾う or config labels を再利用
            $primaryLabel = $this->resolvePrimaryLabel(
                $scored['primary'],
                $scored['candidates'] ?? []
            );

            // result_id を生成（JS が結果画面への遷移に使うID）
            $resultId = 'res_' . Str::random(16);

            // DB 保存
            try {
                DiagnoseResult::create([
                    'result_id'     => $resultId,
                    'primary_type'  => $scored['primary'],
                    'primary_label' => $primaryLabel,
                    'mood'          => $scored['mood'] ?? null,
                    'candidates'    => $scored['candidates'] ?? [],
                    // 'raw_scores' => $scored['scores'] ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('[Diagnose] Failed to save DiagnoseResult', [
                    'error'   => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'result_id' => $resultId,
                    'scored'  => $scored,
                ]);

                return response()->json([
                    'message' => '診断結果の保存に失敗しました。',
                ], 500);
            }

            // これまでの仕様通り result も一緒に返す
            return response()->json([
                'result_id' => $resultId,
                'result'    => [
                    'primary'       => $scored['primary'],
                    'primary_label' => $primaryLabel,
                    'candidates'    => $scored['candidates'] ?? [],
                    'mood'          => $scored['mood'] ?? null,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('[Diagnose] Validation error', [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'message' => 'リクエストが不正です。',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('[Diagnose] Score API error', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'message' => '診断処理中にエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * 診断結果画面
     * GET /diagnose/result/{result_id}
     */
    public function showResult(string $resultId)
    {
        $result = DiagnoseResult::where('result_id', $resultId)->firstOrFail();

        return view('diagnose_result', [
            'result' => $result,
        ]);
    }

    /**
     * primary の label を candidates から解決
     */
    private function resolvePrimaryLabel(string $primary, array $candidates): string
    {
        foreach ($candidates as $row) {
            if (($row['type'] ?? null) === $primary && isset($row['label'])) {
                return (string) $row['label'];
            }
        }

        // 候補に見つからなければ config から拾う
        $labels = config('diagnose.labels', []);

        return (string) ($labels[$primary] ?? $primary);
    }
}
