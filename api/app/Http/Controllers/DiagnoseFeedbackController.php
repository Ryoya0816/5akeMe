<?php

namespace App\Http\Controllers;

use App\Models\DiagnoseFeedback;
use App\Models\DiagnoseResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiagnoseFeedbackController extends Controller
{
    /**
     * フィードバックを保存
     */
    public function store(Request $request, string $resultId): JsonResponse
    {
        // 診断結果を取得
        $result = DiagnoseResult::where('result_id', $resultId)->first();
        
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => '診断結果が見つかりません',
            ], 404);
        }

        // 既にフィードバック済みかチェック
        if ($result->hasFeedback()) {
            return response()->json([
                'success' => false,
                'message' => '既にフィードバック済みです',
            ], 409);
        }

        // バリデーション
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // フィードバックを保存
        $feedback = DiagnoseFeedback::create([
            'diagnose_result_id' => $result->id,
            'rating'             => $validated['rating'],
            'comment'            => $validated['comment'] ?? null,
            // 学習用データ
            'answers_snapshot'   => $result->answers_snapshot,
            'result_type'        => $result->primary_type,
            'mood'               => $result->mood,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'フィードバックありがとうございます！',
            'data'    => [
                'id'     => $feedback->id,
                'rating' => $feedback->rating,
            ],
        ]);
    }

    /**
     * フィードバック済みかチェック
     */
    public function check(string $resultId): JsonResponse
    {
        $result = DiagnoseResult::where('result_id', $resultId)->first();
        
        if (!$result) {
            return response()->json([
                'has_feedback' => false,
            ]);
        }

        $feedback = $result->feedback;

        return response()->json([
            'has_feedback' => $feedback !== null,
            'rating'       => $feedback?->rating,
        ]);
    }
}
