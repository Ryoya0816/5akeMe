<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreSuggestionController extends Controller
{
    public function suggest(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'mood' => 'nullable|integer|in:0,1,2',
            'primary' => 'nullable|string|max:50|alpha_dash',
        ]);

        $mood = (int) ($validated['mood'] ?? 0); // 1 or 2
        $primary = (string) ($validated['primary'] ?? '');

        // 超シンプルなダミーデータ（後でDB化）
        $stores = [
            // mood=1: わいわい
            ['id'=>1,'name'=>'佐賀居酒屋バル 有明','mood'=>1,'tags'=>['大人数','深夜','コスパ']],
            ['id'=>2,'name'=>'呼子ホルモン本舗','mood'=>1,'tags'=>['がっつり','活気']],
            // mood=2: しっとり
            ['id'=>3,'name'=>'鍋島 純米の店','mood'=>2,'tags'=>['静か','日本酒','デート']],
            ['id'=>4,'name'=>'唐津 しっぽり割烹','mood'=>2,'tags'=>['個室','季節']],
        ];

        // moodでフィルタ
        $filtered = array_values(array_filter($stores, fn($s) => $mood ? $s['mood'] === $mood : true));

        // primaryタイプのヒント（簡易ルールで並べ替え）
        if ($primary) {
            usort($filtered, function ($a, $b) use ($primary) {
                $score = fn($s) => in_array($primary, ['junmai','rich_umami']) && str_contains($s['name'], '鍋島') ? 1 : 0;
                return $score($b) <=> $score($a);
            });
        }

        return response()->json(['items' => $filtered, 'count' => count($filtered)]);
    }
}
