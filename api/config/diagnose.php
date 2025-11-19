<?php

return [

    // 採点パラメータ（微調整用）
    'scoring' => [
        'q2_multiplier'   => 1.2,   // 味の好みは少し強めに反映
        'candidate_width' => 3.0,   // 最高点からこの幅以内を候補に残す
    ],

    // タイプ一覧（IDは固定で使う）
    'types' => [
        'craft_beer',       // クラフトビール
        'nihonshu_dry',     // 日本酒 辛口
        'nihonshu_sweet',   // 日本酒 甘口
        'shochu_imo',       // 焼酎 芋
        'shochu_kome',      // 焼酎 米
        'shochu_mugi',      // 焼酎 麦
        'wine_red',         // 赤ワイン
        'wine_white',       // 白ワイン
        'wine_sparkling',   // スパークリングワイン
        'sweet_cocktail',   // 甘いカクテル
    ],

    // 表示ラベル（必要なら結果ページで使用）
    'labels' => [
        'craft_beer'      => 'クラフトビール',
        'nihonshu_dry'    => '日本酒（辛口）',
        'nihonshu_sweet'  => '日本酒（甘口）',
        'shochu_imo'      => '焼酎（芋）',
        'shochu_kome'     => '焼酎（米）',
        'shochu_mugi'     => '焼酎（麦）',
        'wine_red'        => '赤ワイン',
        'wine_white'      => '白ワイン',
        'wine_sparkling'  => 'スパークリングワイン',
        'sweet_cocktail'  => '甘いカクテル',
    ],

    // 固定質問（q1=気分, q2=味の方向）
    'fixed_questions' => [
        [
            'key' => 'q1',
            'text' => '今日はどんな気分？',
            'choices' => [
                ['key' => 'a', 'label' => 'わいわい飲みたい'], // mood=1
                ['key' => 'b', 'label' => 'しっとり飲みたい'], // mood=2
            ],
        ],
        [
            'key' => 'q2',
            'text' => '味の方向性はどっち？',
            'choices' => [
                ['key' => 'a', 'label' => '甘め・軽やか'],
                ['key' => 'b', 'label' => '辛口・キリッと'],
            ],
        ],
    ],

    // カテゴリ質問（教材どおり A/B/C から各1問＝合計5問）
    'categories' => [
        'A' => [
            [
                'key' => 'a1',
                'text' => 'どんなジャンルに惹かれる？',
                'choices' => [
                    ['key' => 'a', 'label' => '日本酒'],
                    ['key' => 'b', 'label' => 'ワイン'],
                    ['key' => 'c', 'label' => 'ビール'],
                ],
            ],
        ],
        'B' => [
            [
                'key' => 'b1',
                'text' => '飲むシーンは？',
                'choices' => [
                    ['key' => 'a', 'label' => '仕事終わりに一杯'],
                    ['key' => 'b', 'label' => '休日にゆっくり'],
                    ['key' => 'c', 'label' => '家飲みでまったり'],
                ],
            ],
        ],
        'C' => [
            [
                'key' => 'c1',
                'text' => 'おつまみは？',
                'choices' => [
                    ['key' => 'a', 'label' => '刺身'],
                    ['key' => 'b', 'label' => '揚げ物'],
                    ['key' => 'c', 'label' => 'チーズ'],
                ],
            ],
        ],
    ],

    /**
     * 選択肢ごとの基本スコア（ジュニア実装：足し算だけ）
     * - キーは "質問キー:選択肢キー"
     * - 値は [タイプID => 加点]
     */
    'weights' => [

        // q2: 味（× q2_multiplier）
        'q2:a' => [ // 甘め・軽やか
            'nihonshu_sweet' => 2.0,
            'sweet_cocktail' => 2.0,
            'wine_white'     => 1.5,
            'wine_sparkling' => 1.5,
            'craft_beer'     => 1.0,
        ],
        'q2:b' => [ // 辛口・キリッと
            'nihonshu_dry'   => 2.0,
            'wine_red'       => 1.5,
            'shochu_imo'     => 1.5,
            'shochu_kome'    => 1.0,
            'shochu_mugi'    => 1.0,
            'craft_beer'     => 1.0,
        ],

        // A: ジャンル志向
        'a1:a' => [ // 日本酒
            'nihonshu_dry'   => 1.5,
            'nihonshu_sweet' => 1.5,
        ],
        'a1:b' => [ // ワイン
            'wine_red'       => 1.5,
            'wine_white'     => 1.5,
            'wine_sparkling' => 1.5,
        ],
        'a1:c' => [ // ビール
            'craft_beer'     => 1.8,
        ],

        // B: シーン
        'b1:a' => [ // 仕事終わりに一杯 → のどごし系や辛口寄り
            'craft_beer'     => 1.5,
            'nihonshu_dry'   => 1.2,
            'shochu_mugi'    => 1.2,
        ],
        'b1:b' => [ // 休日にゆっくり → 甘口/香り/熟成
            'nihonshu_sweet' => 1.2,
            'wine_white'     => 1.2,
            'koshu'          => 0.0, // 互換性のために記載。使わないので0
            'sweet_cocktail' => 1.2,
        ],
        'b1:c' => [ // 家飲みでまったり → 焼酎/日本酒/赤ワイン
            'shochu_imo'     => 1.3,
            'shochu_kome'    => 1.0,
            'nihonshu_dry'   => 1.0,
            'wine_red'       => 1.0,
        ],

        // C: おつまみ
        'c1:a' => [ // 刺身
            'nihonshu_dry'   => 1.4,
            'nihonshu_sweet' => 1.2,
            'wine_white'     => 1.2,
        ],
        'c1:b' => [ // 揚げ物
            'craft_beer'     => 1.4,
            'wine_sparkling' => 1.2,
            'shochu_mugi'    => 1.0,
        ],
        'c1:c' => [ // チーズ
            'wine_red'       => 1.4,
            'wine_white'     => 1.2,
            'sweet_cocktail' => 1.0,
        ],
    ],
];
