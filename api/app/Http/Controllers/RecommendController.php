<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecommendController extends Controller
{
    public function index(Request $req)
    {
        $type = $req->string('sake_type')->toString();
        $mood = (int) $req->integer('mood', 1);
        $limit = (int) $req->integer('limit', 3);

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
