<?php

return [

    // 固定質問（必ず出す2問）
    'fixed' => [

        'q1' => [
            'code'      => 'q1',
            'text'      => '今日の気分は？？',
            'is_fixed'  => true,
            'category'  => null,
            'choices'   => [
                [
                    'code'      => 'q1_1',
                    'text'      => 'わいわいみんなで飲みたい',
                    'mood_tag'  => 'lively',
                    'scores'    => [],
                ],
                [
                    'code'      => 'q1_2',
                    'text'      => '少人数でしっぽりと語りたい',
                    'mood_tag'  => 'chill',
                    'scores'    => [],
                ],
                [
                    'code'      => 'q1_3',
                    'text'      => 'ひとりで静かに飲みたい',
                    'mood_tag'  => 'silent',
                    'scores'    => [],
                ],
                [
                    'code'      => 'q1_4',
                    'text'      => 'サクッと飲みたい',
                    'mood_tag'  => 'light',
                    'scores'    => [],
                ],
                [
                    'code'      => 'q1_5',
                    'text'      => '今夜はがっつり飲みたい',
                    'mood_tag'  => 'strong',
                    'scores'    => [],
                ],
            ],
        ],

        'q2' => [
            'code'      => 'q2',
            'text'      => 'お酒に求めるものは？',
            'is_fixed'  => true,
            'category'  => null,
            'choices'   => [
                [
                    'code'      => 'q2_1',
                    'text'      => '飲みやすさ',
                    'scores'    => [],
                ],
                [
                    'code'      => 'q2_2',
                    'text'      => '香り',
                    'scores'    => [],
                ],
                [
                    'code'      => 'q2_3',
                    'text'      => 'コク',
                    'scores'    => [],
                ],
                [
                    'code'      => 'q2_4',
                    'text'      => 'スッキリ感',
                    'scores'    => [],
                ],
                [
                    'code'      => 'q2_5',
                    'text'      => '料理に合う',
                    'scores'    => [],
                ],
            ],
        ],

    ],

    // カテゴリ別質問（ここからランダム抽出する）
    'categories' => [

        // ==========================
        // A：心理テスト系（性格・世界観）
        // ==========================
        'A' => [

            [
                'code'      => 'A1',
                'text'      => '空気を読む方？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A1_1', 'text' => 'けっこう読む', 'scores' => []],
                    ['code' => 'A1_2', 'text' => 'まぁ読む',     'scores' => []],
                    ['code' => 'A1_3', 'text' => 'ほどほど',     'scores' => []],
                    ['code' => 'A1_4', 'text' => 'あんま読まん', 'scores' => []],
                    ['code' => 'A1_5', 'text' => '自由にいく',   'scores' => []],
                ],
            ],

            [
                'code'      => 'A2',
                'text'      => '自分が主役？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A2_1', 'text' => '主役タイプ',        'scores' => []],
                    ['code' => 'A2_2', 'text' => 'ときどき主役',      'scores' => []],
                    ['code' => 'A2_3', 'text' => '今日は脇役でも',    'scores' => []],
                    ['code' => 'A2_4', 'text' => '裏方が多い',        'scores' => []],
                    ['code' => 'A2_5', 'text' => '主役は苦手',        'scores' => []],
                ],
            ],

            [
                'code'      => 'A3',
                'text'      => '衝動で動くタイプ？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A3_1', 'text' => '衝動多め',       'scores' => []],
                    ['code' => 'A3_2', 'text' => '気分で動く',     'scores' => []],
                    ['code' => 'A3_3', 'text' => '半々',           'scores' => []],
                    ['code' => 'A3_4', 'text' => 'わりと慎重',     'scores' => []],
                    ['code' => 'A3_5', 'text' => 'しっかり慎重',   'scores' => []],
                ],
            ],

            [
                'code'      => 'A4',
                'text'      => '人の温度を感じたい？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A4_1', 'text' => '感じたい',         'scores' => []],
                    ['code' => 'A4_2', 'text' => 'ちょい感じたい',   'scores' => []],
                    ['code' => 'A4_3', 'text' => 'どっちでも',       'scores' => []],
                    ['code' => 'A4_4', 'text' => '距離ほしい',       'scores' => []],
                    ['code' => 'A4_5', 'text' => '一人が楽',         'scores' => []],
                ],
            ],

            [
                'code'      => 'A5',
                'text'      => 'お酒ってどんな存在？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A5_1', 'text' => '親友みたい',      'scores' => []],
                    ['code' => 'A5_2', 'text' => '兄弟みたい',      'scores' => []],
                    ['code' => 'A5_3', 'text' => '恋人みたい',      'scores' => []],
                    ['code' => 'A5_4', 'text' => '先生みたい',      'scores' => []],
                    ['code' => 'A5_5', 'text' => '相棒みたい',      'scores' => []],
                ],
            ],

            [
                'code'      => 'A6',
                'text'      => '想像力は豊かな方？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A6_1', 'text' => 'かなり豊か',      'scores' => []],
                    ['code' => 'A6_2', 'text' => 'けっこうある',    'scores' => []],
                    ['code' => 'A6_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'A6_4', 'text' => 'あんまない',      'scores' => []],
                    ['code' => 'A6_5', 'text' => '現実派',          'scores' => []],
                ],
            ],

            [
                'code'      => 'A7',
                'text'      => '落ち着くのは明るい？暗い？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A7_1', 'text' => '明るい場所',          'scores' => []],
                    ['code' => 'A7_2', 'text' => 'やや明るめ',          'scores' => []],
                    ['code' => 'A7_3', 'text' => 'どっちでも',          'scores' => []],
                    ['code' => 'A7_4', 'text' => '暗いところ！',        'scores' => []],
                    ['code' => 'A7_5', 'text' => '我は深淵が落ち着く',  'scores' => []],
                ],
            ],

            [
                'code'      => 'A8',
                'text'      => '感情は顔に出やすい？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A8_1', 'text' => 'すぐ出ちゃう',    'scores' => []],
                    ['code' => 'A8_2', 'text' => '出やすい',        'scores' => []],
                    ['code' => 'A8_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'A8_4', 'text' => '出にくい',        'scores' => []],
                    ['code' => 'A8_5', 'text' => '氷の仮面',        'scores' => []],
                ],
            ],

            [
                'code'      => 'A9',
                'text'      => '余白のある時間好き？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A9_1', 'text' => '大好き',          'scores' => []],
                    ['code' => 'A9_2', 'text' => 'けっこう好き',    'scores' => []],
                    ['code' => 'A9_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'A9_4', 'text' => 'あまり',          'scores' => []],
                    ['code' => 'A9_5', 'text' => 'いらない',        'scores' => []],
                ],
            ],

            [
                'code'      => 'A10',
                'text'      => '直観と理性どっち？',
                'category'  => 'A',
                'choices'   => [
                    ['code' => 'A10_1', 'text' => '超直感',              'scores' => []],
                    ['code' => 'A10_2', 'text' => '結構直感派',          'scores' => []],
                    ['code' => 'A10_3', 'text' => '半々',                'scores' => []],
                    ['code' => 'A10_4', 'text' => '理性で動きがち',      'scores' => []],
                    ['code' => 'A10_5', 'text' => '理性の塊',            'scores' => []],
                ],
            ],

        ],

        // ==========================
        // B：嗜好（味・香り・度数）
        // ==========================
        'B' => [

            [
                'code'      => 'B1',
                'text'      => '甘口と辛口どっち寄り？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B1_1', 'text' => '甘めが好き',      'scores' => []],
                    ['code' => 'B1_2', 'text' => 'やや甘口',        'scores' => []],
                    ['code' => 'B1_3', 'text' => 'ど真ん中',        'scores' => []],
                    ['code' => 'B1_4', 'text' => 'やや辛口',        'scores' => []],
                    ['code' => 'B1_5', 'text' => '辛口派',          'scores' => []],
                ],
            ],

            [
                'code'      => 'B2',
                'text'      => '香りは強い方が好き？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B2_1', 'text' => 'しっかり香る',    'scores' => []],
                    ['code' => 'B2_2', 'text' => 'ほどよく香る',    'scores' => []],
                    ['code' => 'B2_3', 'text' => '普通でOK',        'scores' => []],
                    ['code' => 'B2_4', 'text' => '控えめでいい',    'scores' => []],
                    ['code' => 'B2_5', 'text' => '香り弱めが好き',  'scores' => []],
                ],
            ],

            [
                'code'      => 'B3',
                'text'      => 'コクが欲しい？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B3_1', 'text' => '深いコク好き',    'scores' => []],
                    ['code' => 'B3_2', 'text' => 'コクやや好き',    'scores' => []],
                    ['code' => 'B3_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'B3_4', 'text' => '軽めがいい',      'scores' => []],
                    ['code' => 'B3_5', 'text' => 'コク弱めが好き',  'scores' => []],
                ],
            ],

            [
                'code'      => 'B4',
                'text'      => '軽めがいい？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B4_1', 'text' => '軽いの希望',      'scores' => []],
                    ['code' => 'B4_2', 'text' => 'やや軽め',        'scores' => []],
                    ['code' => 'B4_3', 'text' => '中間でOK',        'scores' => []],
                    ['code' => 'B4_4', 'text' => 'やや重め',        'scores' => []],
                    ['code' => 'B4_5', 'text' => 'しっかり重め',    'scores' => []],
                ],
            ],

            [
                'code'      => 'B5',
                'text'      => '苦みは平気？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B5_1', 'text' => '苦いの好き',      'scores' => []],
                    ['code' => 'B5_2', 'text' => 'ちょい苦好き',    'scores' => []],
                    ['code' => 'B5_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'B5_4', 'text' => '苦手気味',        'scores' => []],
                    ['code' => 'B5_5', 'text' => '苦いの無理',      'scores' => []],
                ],
            ],

            [
                'code'      => 'B6',
                'text'      => '余韻は長い方が好き？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B6_1', 'text' => '余韻ほしい',      'scores' => []],
                    ['code' => 'B6_2', 'text' => 'ちょい欲しい',    'scores' => []],
                    ['code' => 'B6_3', 'text' => 'どっちでも',      'scores' => []],
                    ['code' => 'B6_4', 'text' => '短めが良き',      'scores' => []],
                    ['code' => 'B6_5', 'text' => '余韻はいらん',    'scores' => []],
                ],
            ],

            [
                'code'      => 'B7',
                'text'      => 'しゅわしゅわ好き？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B7_1', 'text' => '大好き',          'scores' => []],
                    ['code' => 'B7_2', 'text' => 'けっこう好き',    'scores' => []],
                    ['code' => 'B7_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'B7_4', 'text' => 'あまり',          'scores' => []],
                    ['code' => 'B7_5', 'text' => 'しゅわ無理',      'scores' => []],
                ],
            ],

            [
                'code'      => 'B8',
                'text'      => 'クセのある味どう？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B8_1', 'text' => '大歓迎',          'scores' => []],
                    ['code' => 'B8_2', 'text' => '少しなら',        'scores' => []],
                    ['code' => 'B8_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'B8_4', 'text' => '控えめ希望',      'scores' => []],
                    ['code' => 'B8_5', 'text' => 'クセ苦手',        'scores' => []],
                ],
            ],

            [
                'code'      => 'B9',
                'text'      => '度数強めはアリ？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B9_1', 'text' => '強めが好き',      'scores' => []],
                    ['code' => 'B9_2', 'text' => 'ちょい強め',      'scores' => []],
                    ['code' => 'B9_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'B9_4', 'text' => '弱めがいい',      'scores' => []],
                    ['code' => 'B9_5', 'text' => '強いの無理',      'scores' => []],
                ],
            ],

            [
                'code'      => 'B10',
                'text'      => '旨味を感じる飲み物好き？',
                'category'  => 'B',
                'choices'   => [
                    ['code' => 'B10_1', 'text' => '旨味が好き',      'scores' => []],
                    ['code' => 'B10_2', 'text' => 'やや好き',        'scores' => []],
                    ['code' => 'B10_3', 'text' => '普通',            'scores' => []],
                    ['code' => 'B10_4', 'text' => 'あまり',          'scores' => []],
                    ['code' => 'B10_5', 'text' => '旨味は求めない',  'scores' => []],
                ],
            ],

        ],

        // ==========================
        // C：当日の気分・テンション
        // ==========================
        'C' => [

            [
                'code'      => 'C1',
                'text'      => '今日は攻めたい？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C1_1', 'text' => 'めっちゃ攻めたい',  'scores' => []],
                    ['code' => 'C1_2', 'text' => 'けっこう攻める',    'scores' => []],
                    ['code' => 'C1_3', 'text' => '普通にいく',        'scores' => []],
                    ['code' => 'C1_4', 'text' => '控えめで',          'scores' => []],
                    ['code' => 'C1_5', 'text' => 'まったりしたい',    'scores' => []],
                ],
            ],

            [
                'code'      => 'C2',
                'text'      => '今夜はどんな感じ？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C2_1', 'text' => 'わいわいしたい！',  'scores' => []],
                    ['code' => 'C2_2', 'text' => 'ちょい賑やか',      'scores' => []],
                    ['code' => 'C2_3', 'text' => '普通',              'scores' => []],
                    ['code' => 'C2_4', 'text' => '静かめ',            'scores' => []],
                    ['code' => 'C2_5', 'text' => 'まったりしたい',    'scores' => []],
                ],
            ],

            [
                'code'      => 'C3',
                'text'      => '軽めにいく？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C3_1', 'text' => '軽め希望',          'scores' => []],
                    ['code' => 'C3_2', 'text' => 'やや軽め',          'scores' => []],
                    ['code' => 'C3_3', 'text' => '普通でOK',          'scores' => []],
                    ['code' => 'C3_4', 'text' => 'ちょい重め',        'scores' => []],
                    ['code' => 'C3_5', 'text' => 'しっかり飲む',      'scores' => []],
                ],
            ],

            [
                'code'      => 'C4',
                'text'      => '深く飲みたい？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C4_1', 'text' => 'がっつり飲む',      'scores' => []],
                    ['code' => 'C4_2', 'text' => 'やや深め',          'scores' => []],
                    ['code' => 'C4_3', 'text' => '普通',              'scores' => []],
                    ['code' => 'C4_4', 'text' => '軽くでOK',          'scores' => []],
                    ['code' => 'C4_5', 'text' => '今日は控える',      'scores' => []],
                ],
            ],

            [
                'code'      => 'C5',
                'text'      => '今日はどう飲む？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C5_1', 'text' => 'いつもと違うのにチャレンジ！', 'scores' => []],
                    ['code' => 'C5_2', 'text' => 'ちょっと変わったのもあり',     'scores' => []],
                    ['code' => 'C5_3', 'text' => '半々',                          'scores' => []],
                    ['code' => 'C5_4', 'text' => 'ややいつも通り',                'scores' => []],
                    ['code' => 'C5_5', 'text' => 'いつも通りがいい',              'scores' => []],
                ],
            ],

            [
                'code'      => 'C6',
                'text'      => '新しい味試したい？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C6_1', 'text' => 'すごく試したい',    'scores' => []],
                    ['code' => 'C6_2', 'text' => '少し試したい',      'scores' => []],
                    ['code' => 'C6_3', 'text' => 'どっちでも',        'scores' => []],
                    ['code' => 'C6_4', 'text' => 'あまり',            'scores' => []],
                    ['code' => 'C6_5', 'text' => '全然いらん',        'scores' => []],
                ],
            ],

            [
                'code'      => 'C7',
                'text'      => 'ちょい挑戦したい？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C7_1', 'text' => '挑戦したい',        'scores' => []],
                    ['code' => 'C7_2', 'text' => '少しなら',          'scores' => []],
                    ['code' => 'C7_3', 'text' => '普通',              'scores' => []],
                    ['code' => 'C7_4', 'text' => '控えめ',            'scores' => []],
                    ['code' => 'C7_5', 'text' => '挑戦しない',        'scores' => []],
                ],
            ],

            [
                'code'      => 'C8',
                'text'      => '気分変えたい？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C8_1', 'text' => '変えたい',          'scores' => []],
                    ['code' => 'C8_2', 'text' => 'ちょい変えたい',    'scores' => []],
                    ['code' => 'C8_3', 'text' => '普通',              'scores' => []],
                    ['code' => 'C8_4', 'text' => 'あまり',            'scores' => []],
                    ['code' => 'C8_5', 'text' => '今のままで',        'scores' => []],
                ],
            ],

            [
                'code'      => 'C9',
                'text'      => 'エネルギー何％？？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C9_1', 'text' => '100％満タン！！',  'scores' => []],
                    ['code' => 'C9_2', 'text' => '80％',              'scores' => []],
                    ['code' => 'C9_3', 'text' => '60％',              'scores' => []],
                    ['code' => 'C9_4', 'text' => '40％',              'scores' => []],
                    ['code' => 'C9_5', 'text' => '20％未満',          'scores' => []],
                ],
            ],

            [
                'code'      => 'C10',
                'text'      => '今の自分にご褒美を選ぶとしたら？',
                'category'  => 'C',
                'choices'   => [
                    ['code' => 'C10_1', 'text' => '甘いご褒美',        'scores' => []],
                    ['code' => 'C10_2', 'text' => '美味しいお肉',      'scores' => []],
                    ['code' => 'C10_3', 'text' => '自由な時間',        'scores' => []],
                    ['code' => 'C10_4', 'text' => '一日旅行',          'scores' => []],
                    ['code' => 'C10_5', 'text' => '大切な人との時間',  'scores' => []],
                ],
            ],

        ],

    ],

];
