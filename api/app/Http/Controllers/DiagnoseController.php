<?php

namespace App\Http\Controllers;

use App\Services\DiagnoseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DiagnoseController extends Controller
{
    /**
     * POST /api/diagnose/score
     * 期待ボディ: { "answers": { "q1":"a", "q2":"b", "a1":"c", "b1":"a", "c1":"b" } }
     * レスポンス: { "result_id": "res_xxx", "result": { primary, candidates, mood } }
     */
    public function score(Request $request, DiagnoseService $service)
    {
        // 1) バリデーション
        $data = $request->validate([
            'answers' => 'required|array',
            'seed'    => 'nullable|integer',
        ]);

        // 2) 期待キー（固定2 + カテゴリ）を設定から収集
        $fixed = config('diagnose.fixed_questions') ?? [];  // 例: [['key'=>'q1'],['key'=>'q2']]
        $cats  = config('diagnose.categories') ?? [];       // 例: ['A'=>[['key'=>'a1']], 'B'=>...]
        $all   = array_merge($fixed, ...array_values($cats));
        $validKeys = array_column($all, 'key'); // 例: ['q1','q2','a1','b1','c1']

        // 3) 受け取った answers を正規化（"a" でも {"id":"a"} でもOK）
        $normalized = [];
        foreach ($data['answers'] as $qKey => $val) {
            if (!in_array($qKey, $validKeys, true)) continue;

            if (is_array($val)) {
                $choice = $val['id'] ?? $val['key'] ?? $val['value'] ?? null;
                if (is_string($choice) && $choice !== '') {
                    $normalized[$qKey] = $choice;
                }
            } elseif (is_string($val) || is_numeric($val)) {
                $normalized[$qKey] = (string)$val;
            }
        }

        // 4) 必要数チェック（固定2 + カテゴリ3 = 5問）
        if (count($normalized) < 5) {
            $missing = array_values(array_diff($validKeys, array_keys($normalized)));
            Log::warning('[Diagnose] not enough answers', [
                'got_raw'    => $data['answers'],
                'normalized' => $normalized,
                'missing'    => $missing,
            ]);
            return response()->json([
                'message' => 'Not enough answers',
                'need'    => 5,
                'got'     => count($normalized),
                'missing' => $missing,
            ], 422);
        }

        Log::debug('[Diagnose] incoming', [
            'seed'    => $data['seed'] ?? null,
            'answers' => $normalized,
        ]);

        // 5) 採点（★ここで $service を使って一度だけ呼ぶ）
        $result = $service->score($normalized);   // ← Undefined variable 対策はこれでOK

        // 6) 結果保存（セッション境界を避けるため Cache に保存）
        $id = uniqid('res_', true);
        Cache::put("diagnose_result_{$id}", $result, now()->addMinutes(15));

        return response()->json([
            'result_id' => $id,
            'result'    => $result,
        ]);
    }

    /**
     * GET /diagnose/result/{id}
     * Cache から結果を取得して表示
     */
    public function showResult(string $id)
    {
        $data = Cache::get("diagnose_result_{$id}");
        if (!$data) {
            return redirect('/diagnose')->with('error', '診断データが見つかりません。');
        }
        return view('diagnose_result', [
            'result' => $data,
            'id'     => $id,
        ]);
    }
}
