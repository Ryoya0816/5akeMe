<?php

return [

    /*
    |--------------------------------------------------------------------------
    | お酒10種類（診断タイプ）
    |--------------------------------------------------------------------------
    */
    'types' => [
        'sake_dry',
        'sake_sweet',
        'shochu_imo',
        'shochu_kome',
        'shochu_mugi',
        'craft_beer',
        'wine_red',
        'wine_white',
        'cocktail',
        'whisky',
    ],

    /*
    |--------------------------------------------------------------------------
    | 日本語ラベル
    |--------------------------------------------------------------------------
    */
    'labels' => [
        'sake_dry'     => '日本酒・辛口',
        'sake_sweet'   => '日本酒・甘口',
        'shochu_imo'   => '焼酎・芋',
        'shochu_kome'  => '焼酎・米',
        'shochu_mugi'  => '焼酎・麦',
        'craft_beer'   => 'クラフトビール',
        'wine_red'     => '赤ワイン',
        'wine_white'   => '白ワイン',
        'cocktail'     => 'カクテル',
        'whisky'       => 'ウイスキー',
    ],

    /*
    |--------------------------------------------------------------------------
    | スコア調整（q2は1.5倍）
    |--------------------------------------------------------------------------
    */
    'scoring' => [
        'q2_multiplier'   => 1.5,
        'candidate_width' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | 固定2問（q1, q2）
    |--------------------------------------------------------------------------
    */
    'fixed_questions' => [
        [
            'id'   => 'q1',
            'text' => '今日はどんな気分？',
            'choices' => [
                ['label' => 'わいわい飲みたい',       'value' => 'a'],
                ['label' => 'しっとり飲みたい',       'value' => 'b'],
                ['label' => 'ひとりで静かに飲みたい', 'value' => 'c'],
                ['label' => 'サクッと飲みたい',       'value' => 'd'],
                ['label' => 'がっつり飲みたい',       'value' => 'e'],
            ],
        ],
        [
            'id'   => 'q2',
            'text' => 'お酒に何を一番求める？',
            'choices' => [
                ['label' => '飲みやすさ',   'value' => 'a'],
                ['label' => '香り',         'value' => 'b'],
                ['label' => 'コク',         'value' => 'c'],
                ['label' => 'スッキリ感',   'value' => 'd'],
                ['label' => '料理との相性', 'value' => 'e'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カテゴリ別質問（A=心理, B=嗜好, C=気分）
    |--------------------------------------------------------------------------
    */
    'categories' => [

        // ------------------------------------------
        // Aカテゴリ：心理テイスト 10問
        // ------------------------------------------
        'A' => [
            [
                'id'   => 'A1',
                'text' => '初対面の場、あなたはどう動く？',
                'choices' => [
                    ['label' => 'ガンガン喋る',   'value' => 'a'],
                    ['label' => 'そこそこ話す',   'value' => 'b'],
                    ['label' => '様子を見る',     'value' => 'c'],
                    ['label' => '基本聞き役',     'value' => 'd'],
                    ['label' => '完全に静観',     'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A2',
                'text' => 'あなたは普段、どんな立ち位置？',
                'choices' => [
                    ['label' => '主役タイプ',     'value' => 'a'],
                    ['label' => 'ムードメーカー', 'value' => 'b'],
                    ['label' => '今日は脇役でも', 'value' => 'c'],
                    ['label' => '裏方が多い',     'value' => 'd'],
                    ['label' => '影の支配者',     'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A3',
                'text' => '直感派？それとも理性派？',
                'choices' => [
                    ['label' => '超直感派',       'value' => 'a'],
                    ['label' => '結構直感派',     'value' => 'b'],
                    ['label' => '半々',           'value' => 'c'],
                    ['label' => '理性で動きがち', 'value' => 'd'],
                    ['label' => '理性の塊',       'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A4',
                'text' => '人にどう見られたい？',
                'choices' => [
                    ['label' => '面白い人',       'value' => 'a'],
                    ['label' => '優しい人',       'value' => 'b'],
                    ['label' => '頼れる人',       'value' => 'c'],
                    ['label' => '落ち着いた人',   'value' => 'd'],
                    ['label' => 'ミステリアス',   'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A5',
                'text' => 'あなたはストレス溜まると？',
                'choices' => [
                    ['label' => '外に出る',       'value' => 'a'],
                    ['label' => '友達に会う',     'value' => 'b'],
                    ['label' => '趣味に没頭',     'value' => 'c'],
                    ['label' => '静かに考える',   'value' => 'd'],
                    ['label' => '一人で消える',   'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A6',
                'text' => '自分の部屋の理想は？',
                'choices' => [
                    ['label' => '明るい雰囲気',   'value' => 'a'],
                    ['label' => 'ナチュラル',     'value' => 'b'],
                    ['label' => 'どっちでも',     'value' => 'c'],
                    ['label' => 'やや暗め',       'value' => 'd'],
                    ['label' => '暗闇至高',       'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A7',
                'text' => '落ち着くのは明るい？暗い？',
                'choices' => [
                    ['label' => '明るい場所',     'value' => 'a'],
                    ['label' => 'やや明るめ',     'value' => 'b'],
                    ['label' => 'どっちでも',     'value' => 'c'],
                    ['label' => '暗いところ！',   'value' => 'd'],
                    ['label' => '我は深淵が落ち着く', 'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A8',
                'text' => '感情表現するときのタイプは？',
                'choices' => [
                    ['label' => 'すぐ出ちゃう',   'value' => 'a'],
                    ['label' => '出やすい方',     'value' => 'b'],
                    ['label' => '普通',           'value' => 'c'],
                    ['label' => '隠しがち',       'value' => 'd'],
                    ['label' => '氷の仮面',       'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A9',
                'text' => 'あなたにとってお酒はどんな関係？',
                'choices' => [
                    ['label' => '親友',           'value' => 'a'],
                    ['label' => '両親っぽい',     'value' => 'b'],
                    ['label' => '兄弟',           'value' => 'c'],
                    ['label' => '上司',           'value' => 'd'],
                    ['label' => '運命の相棒',     'value' => 'e'],
                ],
            ],
            [
                'id'   => 'A10',
                'text' => 'あなたの判断タイプは？',
                'choices' => [
                    ['label' => '超直感',         'value' => 'a'],
                    ['label' => '結構直感派',     'value' => 'b'],
                    ['label' => '半々',           'value' => 'c'],
                    ['label' => '理性で動く',     'value' => 'd'],
                    ['label' => '理性の塊',       'value' => 'e'],
                ],
            ],
        ],

        // ------------------------------------------
        // Bカテゴリ：嗜好テイスト 10問
        // ------------------------------------------
        'B' => [
            [
                'id'   => 'B1',
                'text' => 'どんなお酒が好き？',
                'choices' => [
                    ['label' => '飲みやすさ重視',     'value' => 'a'],
                    ['label' => '香りを楽しみたい',   'value' => 'b'],
                    ['label' => 'コク深いのが好き',   'value' => 'c'],
                    ['label' => 'すっきり感最優先',   'value' => 'd'],
                    ['label' => '料理に合えばOK',     'value' => 'e'],
                ],
            ],
            [
                'id'   => 'B2',
                'text' => '普段飲むならどんな方向性？',
                'choices' => [
                    ['label' => '甘い系',           'value' => 'a'],
                    ['label' => 'ライト系',         'value' => 'b'],
                    ['label' => 'スタンダード',     'value' => 'c'],
                    ['label' => 'しっかり系',       'value' => 'd'],
                    ['label' => '重め系',           'value' => 'e'],
                ],
            ],
            [
                'id'   => 'B3',
                'text' => '飲み方のこだわりある？',
                'choices' => [
                    ['label' => 'ロックが好き',     'value' => 'a'],
                    ['label' => '水割り',           'value' => 'b'],
                    ['label' => 'ストレート',       'value' => 'c'],
                    ['label' => 'ソーダ割り',       'value' => 'd'],
                    ['label' => '割となんでもOK',   'value' => 'e'],
                ],
            ],
            [
                'id'   => 'B4',
                'text' => '料理と合わせるなら？',
                'choices' => [
                    ['label' => '刺身・魚系',       'value' => 'a'],
                    ['label' => '肉料理',           'value' => 'b'],
                    ['label' => '揚げ物',           'value' => 'c'],
                    ['label' => '野菜・さっぱり',   'value' => 'd'],
                    ['label' => 'チーズ系',         'value' => 'e'],
                ],
            ],
            [
                'id'   => 'B5',
                'text' => 'アルコール度数は？',
                'choices' => [
                    ['label' => '弱めがいい',       'value' => 'a'],
                    ['label' => 'やや弱め',         'value' => 'b'],
                    ['label' => '普通',             'value' => 'c'],
                    ['label' => 'ちょい強め',       'value' => 'd'],
                    ['label' => '強くてもOK',       'value' => 'e'],
                ],
            ],
            [
                'id'   => 'B6',
                'text' => '香りの好みは？',
                'choices' => [
                    ['label' => 'フルーティが好き', 'value' => 'a'],
                    ['label' => 'スッキリ香',       'value' => 'b'],
                    ['label' => '華やか寄り',       'value' => 'c'],
                    ['label' => '落ち着いた香り',   'value' => 'd'],
                    ['label' => '香り強めウェルカム','value' => 'e'],
                ],
            ],
            [
                'id'   => 'B7',
                'text' => 'ボディ感（重さ）は？',
                'choices' => [
                    ['label' => '軽い',             'value' => 'a'],
                    ['label' => 'やや軽い',         'value' => 'b'],
                    ['label' => '普通',             'value' => 'c'],
                    ['label' => 'やや重い',         'value' => 'd'],
                    ['label' => '重いの好き',       'value' => 'e'],
                ],
            ],
            [
                'id'   => 'B8',
                'text' => 'シーンで選ぶなら？',
                'choices' => [
                    ['label' => '仕事終わり',       'value' => 'a'],
                    ['label' => '休日まったり',     'value' => 'b'],
                    ['label' => '外食の時',         'value' => 'c'],
                    ['label' => '家飲み',           'value' => 'd'],
                    ['label' => '特別な日',         'value' => 'e'],
                ],
            ],
            [
                'id'   => 'B9',
                'text' => '一本だけ選ぶなら？',
                'choices' => [
                    ['label' => '甘め',             'value' => 'a'],
                    ['label' => '辛口',             'value' => 'b'],
                    ['label' => 'クセなし',         'value' => 'c'],
                    ['label' => 'クセ強',           'value' => 'd'],
                    ['label' => '重厚',             'value' => 'e'],
                ],
            ],
            [
                'id'   => 'B10',
                'text' => '最後に決め手になるのは？',
                'choices' => [
                    ['label' => '香り',             'value' => 'a'],
                    ['label' => '飲み口',           'value' => 'b'],
                    ['label' => '余韻',             'value' => 'c'],
                    ['label' => '値段',             'value' => 'd'],
                    ['label' => '見た目の雰囲気',   'value' => 'e'],
                ],
            ],
        ],

        // ------------------------------------------
        // Cカテゴリ：気分テイスト 10問
        // ------------------------------------------
        'C' => [
            [
                'id'   => 'C1',
                'text' => '今日はどんな雰囲気で飲みたい？',
                'choices' => [
                    ['label' => 'わいわいしたい！',     'value' => 'a'],
                    ['label' => 'ほどよく話したい',     'value' => 'b'],
                    ['label' => '静かに飲みたい',       'value' => 'c'],
                    ['label' => '1人で落ち着きたい',   'value' => 'd'],
                    ['label' => 'もう無心で飲みたい',   'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C2',
                'text' => '新しいものにチャレンジしたい？',
                'choices' => [
                    ['label' => 'チャレンジしたい！',   'value' => 'a'],
                    ['label' => 'ちょっと変わったのもあり','value' => 'b'],
                    ['label' => '半々かな',             'value' => 'c'],
                    ['label' => 'ややいつも通り',       'value' => 'd'],
                    ['label' => 'いつも通りがいい',     'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C3',
                'text' => '飲む目的はどれに近い？',
                'choices' => [
                    ['label' => '気分転換',             'value' => 'a'],
                    ['label' => '癒されたい',           'value' => 'b'],
                    ['label' => '語りたい',             'value' => 'c'],
                    ['label' => '考えたい',             'value' => 'd'],
                    ['label' => '静かに沈みたい',       'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C4',
                'text' => 'テンションの高さは今どれくらい？',
                'choices' => [
                    ['label' => '120%',               'value' => 'a'],
                    ['label' => '100%',               'value' => 'b'],
                    ['label' => '80%',                'value' => 'c'],
                    ['label' => '50%',                'value' => 'd'],
                    ['label' => '20%未満',           'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C5',
                'text' => '今日はどこに座りたい？',
                'choices' => [
                    ['label' => 'カウンター',         'value' => 'a'],
                    ['label' => 'テーブル小',         'value' => 'b'],
                    ['label' => 'テーブル大',         'value' => 'c'],
                    ['label' => '隅っこ',             'value' => 'd'],
                    ['label' => '個室',               'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C6',
                'text' => 'エネルギー残量はどれくらい？',
                'choices' => [
                    ['label' => '100％満タン！',      'value' => 'a'],
                    ['label' => '80％',               'value' => 'b'],
                    ['label' => '60％',               'value' => 'c'],
                    ['label' => '40％',               'value' => 'd'],
                    ['label' => '20％未満',           'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C7',
                'text' => '飲みの目的は？',
                'choices' => [
                    ['label' => '楽しみたい',         'value' => 'a'],
                    ['label' => '癒されたい',         'value' => 'b'],
                    ['label' => 'リフレッシュ',       'value' => 'c'],
                    ['label' => '考え事',             'value' => 'd'],
                    ['label' => '無になりたい',       'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C8',
                'text' => '今の気持ちを一言で言うなら？',
                'choices' => [
                    ['label' => '最高！',             'value' => 'a'],
                    ['label' => 'いい感じ',           'value' => 'b'],
                    ['label' => '普通',               'value' => 'c'],
                    ['label' => '少し疲れた',         'value' => 'd'],
                    ['label' => 'ぐったり',           'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C9',
                'text' => '今日のご褒美に選ぶなら？',
                'choices' => [
                    ['label' => '甘いご褒美',         'value' => 'a'],
                    ['label' => '美味しいお肉',       'value' => 'b'],
                    ['label' => '自由な時間',         'value' => 'c'],
                    ['label' => '一日旅行',           'value' => 'd'],
                    ['label' => '大切な人との時間',   'value' => 'e'],
                ],
            ],
            [
                'id'   => 'C10',
                'text' => '話しやすいと言われる？',
                'choices' => [
                    ['label' => 'めっちゃ言われる',   'value' => 'a'],
                    ['label' => 'わりと',             'value' => 'b'],
                    ['label' => '普通',               'value' => 'c'],
                    ['label' => 'あんまり',           'value' => 'd'],
                    ['label' => '全く言われない',     'value' => 'e'],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 重み（weights）
    |--------------------------------------------------------------------------
    |
    | ここから下は、りょうやが書いてくれたマッピングを
    | 配列構造だけ整えてそのまま入れている。
    |--------------------------------------------------------------------------
    */
    'weights' => [

        // ==========================================
        // A1: 初対面の場、あなたはどう動く？
        // ==========================================
        'A1:a' => [
            'cocktail'     => 3,
            'craft_beer'   => 3,
            'wine_white'   => 2,
            'sake_sweet'   => 1,
            'shochu_mugi'  => 1,
        ],
        'A1:b' => [
            'craft_beer'   => 2,
            'sake_dry'     => 2,
            'sake_sweet'   => 1,
            'wine_white'   => 1,
            'shochu_mugi'  => 1,
        ],
        'A1:c' => [
            'sake_dry'     => 2,
            'wine_red'     => 2,
            'wine_white'   => 1,
            'shochu_kome'  => 1,
        ],
        'A1:d' => [
            'wine_red'     => 2,
            'sake_dry'     => 2,
            'shochu_kome'  => 1,
            'shochu_mugi'  => 1,
            'whisky'       => 1,
        ],
        'A1:e' => [
            'wine_red'     => 3,
            'whisky'       => 3,
            'shochu_imo'   => 3,
        ],

        // ==========================================
        // A2: あなたは普段、どんな立ち位置？
        // ==========================================
        'A2:a' => [
            'craft_beer'   => 3,
            'wine_red'     => 2,
            'sake_dry'     => 2,
            'cocktail'     => 2,
            'shochu_mugi'  => 1,
        ],
        'A2:b' => [
            'cocktail'     => 3,
            'craft_beer'   => 3,
            'sake_sweet'   => 2,
            'wine_white'   => 1,
            'sake_dry'     => 1,
        ],
        'A2:c' => [
            'wine_white'   => 2,
            'shochu_mugi'  => 2,
            'sake_dry'     => 1,
            'sake_sweet'   => 1,
            'shochu_kome'  => 1,
        ],
        'A2:d' => [
            'shochu_kome'  => 3,
            'sake_dry'     => 2,
            'wine_red'     => 1,
            'shochu_mugi'  => 1,
            'whisky'       => 1,
        ],
        'A2:e' => [
            'whisky'       => 3,
            'wine_red'     => 3,
            'shochu_imo'   => 3,
        ],

        // ==========================================
        // A3: 直感派？それとも理性派？
        // ==========================================
        'A3:a' => [
            'cocktail'     => 3,
            'wine_white'   => 2,
            'craft_beer'   => 2,
            'sake_sweet'   => 1,
            'shochu_mugi'  => 1,
        ],
        'A3:b' => [
            'craft_beer'   => 2,
            'cocktail'     => 2,
            'wine_white'   => 2,
            'sake_dry'     => 1,
            'sake_sweet'   => 1,
        ],
        'A3:c' => [
            'sake_dry'     => 2,
            'sake_sweet'   => 2,
            'shochu_mugi'  => 2,
            'wine_red'     => 1,
            'wine_white'   => 1,
        ],
        'A3:d' => [
            'sake_dry'     => 3,
            'wine_red'     => 2,
            'shochu_kome'  => 2,
            'whisky'       => 1,
        ],
        'A3:e' => [
            'sake_dry'     => 3,
            'wine_red'     => 3,
            'whisky'       => 3,
        ],

        // ==========================================
        // A4: 人にどう見られたい？
        // ==========================================
        'A4:a' => [
            'cocktail'     => 3,
            'craft_beer'   => 3,
            'wine_white'   => 2,
            'sake_sweet'   => 1,
        ],
        'A4:b' => [
            'sake_sweet'   => 3,
            'wine_white'   => 2,
            'shochu_kome'  => 2,
            'cocktail'     => 1,
        ],
        'A4:c' => [
            'sake_dry'     => 3,
            'wine_red'     => 3,
            'craft_beer'   => 2,
            'shochu_mugi'  => 1,
            'whisky'       => 1,
        ],
        'A4:d' => [
            'wine_red'     => 3,
            'whisky'       => 3,
            'shochu_kome'  => 2,
            'sake_dry'     => 1,
        ],
        'A4:e' => [
            'whisky'       => 3,
            'shochu_imo'   => 3,
            'wine_red'     => 2,
        ],

        // ==========================================
        // A5: あなたはストレス溜まると？
        // ==========================================
        'A5:a' => [
            'craft_beer'   => 3,
            'cocktail'     => 2,
            'sake_dry'     => 1,
            'wine_white'   => 1,
        ],
        'A5:b' => [
            'craft_beer'   => 3,
            'cocktail'     => 3,
            'sake_sweet'   => 1,
            'wine_white'   => 1,
        ],
        'A5:c' => [
            'sake_dry'     => 2,
            'wine_red'     => 2,
            'shochu_mugi'  => 2,
            'craft_beer'   => 1,
        ],
        'A5:d' => [
            'wine_red'     => 3,
            'whisky'       => 2,
            'shochu_kome'  => 2,
            'sake_dry'     => 1,
        ],
        'A5:e' => [
            'whisky'       => 3,
            'shochu_imo'   => 3,
            'wine_red'     => 2,
        ],

        // ==========================================
        // A6: 自分の部屋の理想は？
        // ==========================================
        'A6:a' => [
            'wine_white'   => 3,
            'cocktail'     => 2,
            'sake_sweet'   => 2,
            'craft_beer'   => 1,
        ],
        'A6:b' => [
            'sake_sweet'   => 2,
            'shochu_kome'  => 2,
            'sake_dry'     => 1,
            'wine_white'   => 1,
        ],
        'A6:c' => [
            'sake_dry'     => 1,
            'sake_sweet'   => 1,
            'shochu_mugi'  => 1,
        ],
        'A6:d' => [
            'wine_red'     => 3,
            'whisky'       => 2,
            'shochu_kome'  => 2,
        ],
        'A6:e' => [
            'whisky'       => 3,
            'shochu_imo'   => 3,
            'wine_red'     => 2,
        ],

        // ==========================================
        // A7: 落ち着くのは明るい？暗い？
        // ==========================================
        'A7:a' => [
            'wine_white'   => 3,
            'cocktail'     => 2,
            'craft_beer'   => 2,
        ],
        'A7:b' => [
            'wine_white'   => 2,
            'craft_beer'   => 2,
            'sake_sweet'   => 1,
        ],
        'A7:c' => [
            'sake_dry'     => 1,
            'shochu_mugi'  => 1,
            'craft_beer'   => 1,
        ],
        'A7:d' => [
            'wine_red'     => 3,
            'shochu_imo'   => 2,
            'whisky'       => 2,
        ],
        'A7:e' => [
            'whisky'       => 3,
            'shochu_imo'   => 3,
            'wine_red'     => 3,
        ],

        // ==========================================
        // A8: 感情表現するときのタイプは？
        // ==========================================
        'A8:a' => [
            'cocktail'     => 3,
            'craft_beer'   => 2,
            'wine_white'   => 2,
        ],
        'A8:b' => [
            'craft_beer'   => 2,
            'wine_white'   => 2,
            'sake_sweet'   => 1,
        ],
        'A8:c' => [
            'sake_dry'     => 1,
            'sake_sweet'   => 1,
            'shochu_mugi'  => 1,
        ],
        'A8:d' => [
            'wine_red'     => 2,
            'sake_dry'     => 2,
            'shochu_kome'  => 2,
        ],
        'A8:e' => [
            'whisky'       => 3,
            'wine_red'     => 3,
            'shochu_imo'   => 3,
        ],

        // ==========================================
        // A9: あなたにとってお酒はどんな関係？
        // ==========================================
        'A9:a' => [
            'cocktail'     => 3,
            'craft_beer'   => 2,
            'wine_white'   => 1,
        ],
        'A9:b' => [
            'sake_sweet'   => 3,
            'shochu_kome'  => 2,
            'wine_white'   => 1,
        ],
        'A9:c' => [
            'sake_dry'     => 2,
            'shochu_mugi'  => 2,
            'craft_beer'   => 1,
        ],
        'A9:d' => [
            'wine_red'     => 3,
            'whisky'       => 2,
            'sake_dry'     => 1,
        ],
        'A9:e' => [
            'whisky'       => 3,
            'shochu_imo'   => 3,
            'wine_red'     => 2,
        ],

        // ==========================================
        // A10: あなたの判断タイプは？
        // ==========================================
        'A10:a' => [
            'cocktail'     => 3,
            'wine_white'   => 2,
            'craft_beer'   => 1,
        ],
        'A10:b' => [
            'wine_white'   => 2,
            'craft_beer'   => 2,
            'sake_sweet'   => 1,
        ],
        'A10:c' => [
            'sake_dry'     => 2,
            'shochu_mugi'  => 1,
            'wine_white'   => 1,
        ],
        'A10:d' => [
            'sake_dry'     => 3,
            'wine_red'     => 2,
            'whisky'       => 1,
        ],
        'A10:e' => [
            'wine_red'     => 3,
            'whisky'       => 3,
            'shochu_imo'   => 2,
        ],

        // ==========================================
        // B1: どんなお酒が好き？
        // ==========================================
        'B1:a' => [
            'sake_sweet'   => 5,
            'cocktail'     => 5,
            'wine_white'   => 4,
            'shochu_mugi'  => 3,
        ],
        'B1:b' => [
            'wine_white'   => 5,
            'sake_sweet'   => 4,
            'craft_beer'   => 4,
            'wine_red'     => 3,
        ],
        'B1:c' => [
            'wine_red'     => 5,
            'shochu_imo'   => 5,
            'whisky'       => 5,
            'craft_beer'   => 4,
        ],
        'B1:d' => [
            'sake_dry'     => 5,
            'wine_white'   => 4,
            'shochu_kome'  => 3,
        ],
        'B1:e' => [
            'sake_dry'     => 4,
            'sake_sweet'   => 4,
            'shochu_mugi'  => 3,
            'wine_white'   => 3,
        ],

        // ==========================================
        // B2: 普段飲むならどんな方向性？
        // ==========================================
        'B2:a' => [
            'sake_sweet'   => 5,
            'cocktail'     => 5,
            'wine_white'   => 4,
        ],
        'B2:b' => [
            'wine_white'   => 5,
            'shochu_mugi'  => 4,
            'sake_sweet'   => 3,
        ],
        'B2:c' => [
            'sake_dry'     => 4,
            'shochu_mugi'  => 4,
            'craft_beer'   => 3,
        ],
        'B2:d' => [
            'wine_red'     => 5,
            'shochu_imo'   => 4,
            'craft_beer'   => 4,
        ],
        'B2:e' => [
            'wine_red'     => 5,
            'shochu_imo'   => 5,
            'whisky'       => 5,
        ],

        // ==========================================
        // B3: 飲み方のこだわりある？
        // ==========================================
        'B3:a' => [
            'whisky'       => 5,
            'shochu_imo'   => 4,
            'wine_red'     => 3,
        ],
        'B3:b' => [
            'shochu_kome'  => 5,
            'shochu_mugi'  => 4,
            'sake_sweet'   => 3,
        ],
        'B3:c' => [
            'whisky'       => 5,
            'shochu_imo'   => 4,
            'wine_red'     => 4,
        ],
        'B3:d' => [
            'cocktail'     => 5,
            'craft_beer'   => 4,
            'sake_sweet'   => 3,
        ],
        'B3:e' => [
            'sake_dry'     => 3,
            'sake_sweet'   => 3,
            'shochu_mugi'  => 3,
            'wine_white'   => 3,
        ],

        // ==========================================
        // B4: 料理と合わせるなら？
        // ==========================================
        'B4:a' => [
            'sake_dry'     => 5,
            'wine_white'   => 5,
            'shochu_kome'  => 4,
        ],
        'B4:b' => [
            'wine_red'     => 5,
            'shochu_imo'   => 5,
            'whisky'       => 4,
        ],
        'B4:c' => [
            'shochu_mugi'  => 5,
            'craft_beer'   => 4,
            'sake_sweet'   => 3,
        ],
        'B4:d' => [
            'wine_white'   => 5,
            'sake_dry'     => 4,
            'shochu_mugi'  => 3,
        ],
        'B4:e' => [
            'wine_white'   => 5,
            'wine_red'     => 4,
            'craft_beer'   => 3,
        ],

        // ==========================================
        // B5: アルコール度数は？
        // ==========================================
        'B5:a' => [
            'cocktail'     => 5,
            'wine_white'   => 4,
            'sake_sweet'   => 3,
        ],
        'B5:b' => [
            'sake_sweet'   => 4,
            'wine_white'   => 3,
            'shochu_mugi'  => 3,
        ],
        'B5:c' => [
            'sake_dry'     => 4,
            'shochu_mugi'  => 3,
            'wine_white'   => 3,
        ],
        'B5:d' => [
            'wine_red'     => 5,
            'shochu_imo'   => 4,
            'craft_beer'   => 3,
        ],
        'B5:e' => [
            'whisky'       => 5,
            'shochu_imo'   => 5,
            'wine_red'     => 4,
        ],

        // ==========================================
        // B6: 香りの好みは？
        // ==========================================
        'B6:a' => [
            'wine_white'   => 5,
            'sake_sweet'   => 4,
            'cocktail'     => 4,
        ],
        'B6:b' => [
            'sake_dry'     => 5,
            'shochu_kome'  => 4,
            'wine_white'   => 3,
        ],
        'B6:c' => [
            'wine_white'   => 5,
            'sake_sweet'   => 4,
            'craft_beer'   => 3,
        ],
        'B6:d' => [
            'wine_red'     => 5,
            'shochu_imo'   => 4,
            'shochu_mugi'  => 3,
        ],
        'B6:e' => [
            'whisky'       => 5,
            'shochu_imo'   => 5,
            'wine_red'     => 4,
        ],

        // ==========================================
        // B7: ボディ感（重さ）は？
        // ==========================================
        'B7:a' => [
            'wine_white'   => 5,
            'sake_sweet'   => 4,
            'shochu_mugi'  => 3,
        ],
        'B7:b' => [
            'wine_white'   => 4,
            'shochu_mugi'  => 3,
            'sake_sweet'   => 3,
        ],
        'B7:c' => [
            'sake_dry'     => 4,
            'shochu_mugi'  => 3,
            'craft_beer'   => 3,
        ],
        'B7:d' => [
            'wine_red'     => 5,
            'craft_beer'   => 4,
            'shochu_imo'   => 3,
        ],
        'B7:e' => [
            'wine_red'     => 5,
            'shochu_imo'   => 5,
            'whisky'       => 5,
        ],

        // ==========================================
        // B8: シーンで選ぶなら？
        // ==========================================
        'B8:a' => [
            'craft_beer'   => 5,
            'sake_dry'     => 4,
            'shochu_mugi'  => 3,
        ],
        'B8:b' => [
            'wine_white'   => 4,
            'sake_sweet'   => 4,
            'shochu_kome'  => 3,
        ],
        'B8:c' => [
            'wine_red'     => 4,
            'wine_white'   => 4,
            'sake_dry'     => 3,
        ],
        'B8:d' => [
            'shochu_mugi'  => 4,
            'sake_sweet'   => 3,
            'shochu_kome'  => 3,
        ],
        'B8:e' => [
            'wine_red'     => 5,
            'whisky'       => 5,
            'shochu_imo'   => 4,
        ],

        // ==========================================
        // B9: 一本だけ選ぶなら？
        // ==========================================
        'B9:a' => [
            'sake_sweet'   => 5,
            'cocktail'     => 4,
            'wine_white'   => 4,
        ],
        'B9:b' => [
            'sake_dry'     => 5,
            'wine_white'   => 3,
            'shochu_kome'  => 3,
        ],
        'B9:c' => [
            'shochu_mugi'  => 4,
            'sake_sweet'   => 3,
            'sake_dry'     => 3,
        ],
        'B9:d' => [
            'shochu_imo'   => 5,
            'craft_beer'   => 4,
            'wine_red'     => 4,
        ],
        'B9:e' => [
            'whisky'       => 5,
            'wine_red'     => 5,
            'shochu_imo'   => 4,
        ],

        // ==========================================
        // B10: 最後に決め手になるのは？
        // ==========================================
        'B10:a' => [
            'wine_white'   => 5,
            'wine_red'     => 4,
            'sake_sweet'   => 3,
        ],
        'B10:b' => [
            'sake_sweet'   => 4,
            'sake_dry'     => 4,
            'shochu_mugi'  => 3,
        ],
        'B10:c' => [
            'whisky'       => 5,
            'wine_red'     => 4,
            'shochu_imo'   => 4,
        ],
        'B10:d' => [
            'shochu_mugi'  => 4,
            'sake_dry'     => 3,
            'sake_sweet'   => 3,
        ],
        'B10:e' => [
            'cocktail'     => 5,
            'wine_white'   => 4,
            'craft_beer'   => 4,
        ],

        // ==========================================
        // C1: 今日はどんな雰囲気で飲みたい？
        // ==========================================
        'C1:a' => [
            'craft_beer'   => 5,
            'cocktail'     => 4,
            'wine_white'   => 3,
        ],
        'C1:b' => [
            'sake_sweet'   => 3,
            'wine_white'   => 3,
            'shochu_mugi'  => 2,
        ],
        'C1:c' => [
            'wine_red'     => 4,
            'shochu_kome'  => 3,
            'sake_dry'     => 2,
        ],
        'C1:d' => [
            'whisky'       => 4,
            'wine_red'     => 3,
            'shochu_imo'   => 3,
        ],
        'C1:e' => [
            'whisky'       => 5,
            'shochu_imo'   => 4,
            'wine_red'     => 4,
        ],

        // ==========================================
        // C2
        // ==========================================
        'C2:a' => [
            'craft_beer'   => 5,
            'cocktail'     => 4,
            'wine_white'   => 3,
        ],
        'C2:b' => [
            'craft_beer'   => 4,
            'wine_white'   => 3,
            'sake_sweet'   => 2,
        ],
        'C2:c' => [
            'sake_dry'     => 2,
            'sake_sweet'   => 2,
            'shochu_mugi'  => 2,
        ],
        'C2:d' => [
            'wine_red'     => 3,
            'shochu_kome'  => 2,
            'whisky'       => 2,
        ],
        'C2:e' => [
            'wine_red'     => 4,
            'shochu_imo'   => 3,
            'whisky'       => 3,
        ],

        // ==========================================
        // C3
        // ==========================================
        'C3:a' => [
            'craft_beer'   => 4,
            'cocktail'     => 3,
            'wine_white'   => 2,
        ],
        'C3:b' => [
            'sake_sweet'   => 4,
            'wine_white'   => 3,
            'shochu_kome'  => 2,
        ],
        'C3:c' => [
            'wine_red'     => 4,
            'craft_beer'   => 3,
            'sake_dry'     => 2,
        ],
        'C3:d' => [
            'whisky'       => 4,
            'wine_red'     => 3,
            'shochu_imo'   => 2,
        ],
        'C3:e' => [
            'whisky'       => 5,
            'shochu_imo'   => 4,
            'wine_red'     => 3,
        ],

        // ==========================================
        // C4
        // ==========================================
        'C4:a' => [
            'craft_beer'   => 5,
            'cocktail'     => 5,
            'wine_white'   => 3,
        ],
        'C4:b' => [
            'craft_beer'   => 4,
            'cocktail'     => 4,
            'sake_sweet'   => 3,
        ],
        'C4:c' => [
            'sake_dry'     => 2,
            'shochu_mugi'  => 2,
            'wine_white'   => 1,
        ],
        'C4:d' => [
            'wine_red'     => 3,
            'shochu_kome'  => 2,
            'sake_dry'     => 2,
        ],
        'C4:e' => [
            'whisky'       => 4,
            'shochu_imo'   => 3,
            'wine_red'     => 3,
        ],

        // ==========================================
        // C5
        // ==========================================
        'C5:a' => [
            'craft_beer'   => 4,
            'sake_dry'     => 3,
            'cocktail'     => 2,
        ],
        'C5:b' => [
            'sake_sweet'   => 3,
            'wine_white'   => 2,
            'shochu_mugi'  => 2,
        ],
        'C5:c' => [
            'craft_beer'   => 3,
            'wine_white'   => 2,
            'sake_dry'     => 2,
        ],
        'C5:d' => [
            'wine_red'     => 4,
            'shochu_kome'  => 3,
            'whisky'       => 3,
        ],
        'C5:e' => [
            'whisky'       => 5,
            'wine_red'     => 4,
            'shochu_imo'   => 4,
        ],

        // ==========================================
        // C6
        // ==========================================
        'C6:a' => [
            'craft_beer'   => 5,
            'cocktail'     => 4,
            'wine_white'   => 3,
        ],
        'C6:b' => [
            'craft_beer'   => 4,
            'sake_sweet'   => 3,
            'wine_white'   => 2,
        ],
        'C6:c' => [
            'sake_dry'     => 3,
            'shochu_mugi'  => 2,
            'wine_white'   => 1,
        ],
        'C6:d' => [
            'wine_red'     => 3,
            'shochu_kome'  => 2,
            'sake_dry'     => 2,
        ],
        'C6:e' => [
            'whisky'       => 4,
            'shochu_imo'   => 4,
            'wine_red'     => 3,
        ],

        // ==========================================
        // C7
        // ==========================================
        'C7:a' => [
            'craft_beer'   => 5,
            'cocktail'     => 4,
            'wine_white'   => 3,
        ],
        'C7:b' => [
            'sake_sweet'   => 4,
            'wine_white'   => 3,
            'shochu_kome'  => 2,
        ],
        'C7:c' => [
            'sake_dry'     => 3,
            'craft_beer'   => 3,
            'wine_white'   => 2,
        ],
        'C7:d' => [
            'wine_red'     => 4,
            'shochu_imo'   => 3,
            'whisky'       => 3,
        ],
        'C7:e' => [
            'whisky'       => 5,
            'shochu_imo'   => 4,
            'wine_red'     => 3,
        ],

        // ==========================================
        // C8
        // ==========================================
        'C8:a' => [
            'craft_beer'   => 5,
            'cocktail'     => 4,
        ],
        'C8:b' => [
            'sake_sweet'   => 3,
            'wine_white'   => 2,
        ],
        'C8:c' => [
            'sake_dry'     => 2,
            'shochu_mugi'  => 2,
        ],
        'C8:d' => [
            'wine_red'     => 3,
            'shochu_kome'  => 2,
        ],
        'C8:e' => [
            'whisky'       => 5,
            'shochu_imo'   => 4,
        ],

        // ==========================================
        // C9
        // ==========================================
        'C9:a' => [
            'sake_sweet'   => 5,
            'cocktail'     => 4,
        ],
        'C9:b' => [
            'wine_red'     => 5,
            'shochu_imo'   => 4,
            'whisky'       => 3,
        ],
        'C9:c' => [
            'sake_dry'     => 3,
            'shochu_mugi'  => 2,
        ],
        'C9:d' => [
            'craft_beer'   => 4,
            'wine_white'   => 3,
        ],
        'C9:e' => [
            'wine_white'   => 4,
            'sake_sweet'   => 3,
        ],

        // ==========================================
        // C10
        // ==========================================
        'C10:a' => [
            'cocktail'     => 4,
            'craft_beer'   => 4,
            'wine_white'   => 2,
        ],
        'C10:b' => [
            'sake_sweet'   => 3,
            'wine_white'   => 2,
        ],
        'C10:c' => [
            'sake_dry'     => 2,
            'shochu_mugi'  => 2,
        ],
        'C10:d' => [
            'wine_red'     => 3,
            'shochu_kome'  => 2,
        ],
        'C10:e' => [
            'whisky'       => 5,
            'shochu_imo'   => 4,
            'wine_red'     => 3,
        ],

        // ==========================================
        // q2（1.5倍適用対象）
        // ==========================================
        'q2:a' => [
            'sake_sweet'   => 6,
            'wine_white'   => 5,
            'cocktail'     => 5,
            'shochu_mugi'  => 3,
        ],
        'q2:b' => [
            'wine_white'   => 6,
            'wine_red'     => 5,
            'sake_sweet'   => 4,
            'craft_beer'   => 3,
        ],
        'q2:c' => [
            'wine_red'     => 6,
            'shochu_imo'   => 6,
            'whisky'       => 6,
            'craft_beer'   => 4,
        ],
        'q2:d' => [
            'sake_dry'     => 6,
            'wine_white'   => 5,
            'shochu_kome'  => 4,
        ],
        'q2:e' => [
            'sake_dry'     => 5,
            'sake_sweet'   => 5,
            'shochu_mugi'  => 4,
            'wine_white'   => 3,
        ],
    ],

];
