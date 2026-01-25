<?php

namespace App\Http\Controllers;

use App\Models\DiagnoseResult;
use App\Services\DiagnoseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
                'answers'   => 'required|array|min:1|max:10',
                'answers.*' => 'required|string|max:50',
                'seed'      => 'nullable|integer|min:0|max:999999999',
            ]);

            $answers = $data['answers'];
            $seed    = $data['seed'] ?? null;

            // 追加のセキュリティチェック: 不正なキーを除外
            $allowedKeys = ['q1', 'q2', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10',
                           'B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8', 'B9', 'B10',
                           'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10'];
            $answers = array_filter(
                $answers,
                fn($key) => in_array($key, $allowedKeys, true),
                ARRAY_FILTER_USE_KEY
            );

            // Python AIサービスでスコア計算
            $scored = $this->callPythonScoreApi($answers, $seed);
            
            // Python APIがエラーの場合はPHPにフォールバック
            if ($scored === null) {
                Log::warning('[Diagnose] Python API failed, falling back to PHP');
                $scored = $service->score($answers, $seed);
            }

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

            // primaryLabel が空の場合は primary_type をそのまま使用
            if (empty($primaryLabel)) {
                $primaryLabel = $scored['primary'];
            }

            // result_id を生成（JS が結果画面への遷移に使うID）
            $resultId = 'res_' . Str::random(16);

            // DB 保存
            try {
                $result = DiagnoseResult::create([
                    'result_id'       => $resultId,
                    'primary_type'    => $scored['primary'],
                    'primary_label'   => $primaryLabel,
                    'mood'            => $scored['mood'] ?? null,
                    'candidates'      => $scored['candidates'] ?? [],
                    'top5'            => $scored['top5'] ?? [],
                    'answers_snapshot' => $answers,  // 学習用に回答パターンを保存
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('[Diagnose] Failed to save DiagnoseResult (QueryException)', [
                    'error'     => $e->getMessage(),
                    'sql'       => $e->getSql() ?? null,
                    'bindings'  => $e->getBindings() ?? null,
                    'result_id' => $resultId,
                    'scored'    => $scored,
                ]);

                return response()->json([
                    'message' => '診断結果の保存に失敗しました。',
                    'error'   => app()->isProduction() ? null : $e->getMessage(),
                ], 500);
            } catch (\Exception $e) {
                Log::error('[Diagnose] Failed to save DiagnoseResult', [
                    'error'     => $e->getMessage(),
                    'trace'     => $e->getTraceAsString(),
                    'result_id' => $resultId,
                    'scored'    => $scored,
                ]);

                return response()->json([
                    'message' => '診断結果の保存に失敗しました。',
                    'error'   => app()->isProduction() ? null : $e->getMessage(),
                ], 500);
            }

            // これまでの仕様通り result も一緒に返す
            return response()->json([
                'result_id' => $resultId,
                'result'    => [
                    'primary'       => $scored['primary'],
                    'primary_label' => $primaryLabel,
                    'candidates'    => $scored['candidates'] ?? [],
                    'top5'          => $scored['top5'] ?? [],
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

        // ログインユーザーなら診断結果を紐付け
        if (auth()->check()) {
            $user = auth()->user();
            // 既に紐付けられていなければ追加
            if (!$user->diagnoseResults()->where('diagnose_result_id', $result->id)->exists()) {
                $user->diagnoseResults()->attach($result->id);
            }
        }

        // おすすめ店舗を取得（お酒タイプ + 雰囲気でマッチング）
        $stores = $this->getRecommendedStores($result);

        return view('diagnose_result', [
            'result' => $result,
            'stores' => $stores,
        ]);
    }

    /**
     * 診断結果に基づいておすすめ店舗を取得
     */
    private function getRecommendedStores(DiagnoseResult $result): \Illuminate\Support\Collection
    {
        $primaryType = $result->primary_type;
        $mood = $result->mood;

        // 店舗モデルをインポート
        $storeQuery = \App\Models\Store::active();

        // 雰囲気でフィルタリング（moodがあれば）
        if ($mood) {
            $storeQuery->where(function ($q) use ($mood) {
                $q->where('mood', $mood)
                  ->orWhere('mood', 'both');
            });
        }

        // お酒タイプでフィルタリング
        if ($primaryType) {
            $storeQuery->where(function ($q) use ($primaryType) {
                $q->whereJsonContains('sake_types', $primaryType);
            });
        }

        // 最大3件取得（ランダム順）
        $stores = $storeQuery->inRandomOrder()->limit(3)->get();

        // 3件に満たない場合は、条件を緩和して追加取得
        if ($stores->count() < 3) {
            $existingIds = $stores->pluck('id')->toArray();
            $remaining = 3 - $stores->count();

            // 雰囲気だけでマッチング
            $additionalStores = \App\Models\Store::active()
                ->whereNotIn('id', $existingIds)
                ->where(function ($q) use ($mood) {
                    if ($mood) {
                        $q->where('mood', $mood)
                          ->orWhere('mood', 'both');
                    }
                })
                ->inRandomOrder()
                ->limit($remaining)
                ->get();

            $stores = $stores->merge($additionalStores);
        }

        // それでも足りなければ、アクティブな店舗からランダム取得
        if ($stores->count() < 3) {
            $existingIds = $stores->pluck('id')->toArray();
            $remaining = 3 - $stores->count();

            $additionalStores = \App\Models\Store::active()
                ->whereNotIn('id', $existingIds)
                ->inRandomOrder()
                ->limit($remaining)
                ->get();

            $stores = $stores->merge($additionalStores);
        }

        return $stores;
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

    /**
     * Python AIサービスのスコア計算APIを呼び出す
     * 
     * @param array $answers 回答データ
     * @param int|null $seed シード値
     * @return array|null スコア結果、失敗時はnull
     */
    private function callPythonScoreApi(array $answers, ?int $seed = null): ?array
    {
        try {
            // Docker内部ネットワークでPythonコンテナに接続（config経由で取得）
            $pythonApiUrl = config('services.python_api.url', 'http://python:8000');
            
            $response = Http::timeout(10)
                ->post("{$pythonApiUrl}/score", [
                    'answers' => $answers,
                    'seed'    => $seed,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('[Diagnose] Python API success', [
                    'primary' => $data['primary'] ?? null,
                    'mood'    => $data['mood'] ?? null,
                ]);

                return [
                    'primary'    => $data['primary'] ?? null,
                    'candidates' => $data['candidates'] ?? [],
                    'top5'       => $data['top5'] ?? [],
                    'mood'       => $data['mood'] ?? null,
                    'scores_map' => $data['scores'] ?? [],
                ];
            }

            Log::warning('[Diagnose] Python API returned error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('[Diagnose] Python API connection failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
