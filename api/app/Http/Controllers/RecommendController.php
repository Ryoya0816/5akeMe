<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecommendController extends Controller
{
    public function index(Request $req)
    {
        // バリデーション
        $validated = $req->validate([
            'sake_type' => 'nullable|string|max:50|alpha_dash',
            'mood' => 'nullable|integer|in:1,2',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $type = $validated['sake_type'] ?? '';
        $mood = (int) ($validated['mood'] ?? 1);
        $limit = (int) ($validated['limit'] ?? 3);

        $map = config('sake_map', []);
        $list = $map[$type] ?? [];

        // moodが2（しっとり）なら温度・ペアリングを“落ち着き寄り”に並び替えるなどの素朴な調整
        if ($mood === 2) {
            usort($list, fn($a,$b) => strcmp($a['serve'], $b['serve'])); // 超簡易
        }

        return response()->json([
            'type' => $type,
            'mood' => $mood,
            'items' => array_slice($list, 0, max(1,$limit)),
        ]);
    }
}
