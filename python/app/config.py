"""
5akeMe 診断設定

お酒タイプ、ラベル、デフォルトの重みを定義
"""

# お酒10種類
SAKE_TYPES = [
    'sake_dry',      # 日本酒・辛口
    'sake_sweet',    # 日本酒・甘口
    'shochu_imo',    # 焼酎・芋
    'shochu_kome',   # 焼酎・米
    'shochu_mugi',   # 焼酎・麦
    'craft_beer',    # クラフトビール
    'wine_red',      # 赤ワイン
    'wine_white',    # 白ワイン
    'cocktail',      # カクテル
    'whisky',        # ウイスキー
]

# 日本語ラベル
LABELS = {
    'sake_dry': '日本酒・辛口',
    'sake_sweet': '日本酒・甘口',
    'shochu_imo': '焼酎・芋',
    'shochu_kome': '焼酎・米',
    'shochu_mugi': '焼酎・麦',
    'craft_beer': 'クラフトビール',
    'wine_red': '赤ワイン',
    'wine_white': '白ワイン',
    'cocktail': 'カクテル',
    'whisky': 'ウイスキー',
}

# 気分マッピング（q1の回答 -> mood）
MOOD_MAP = {
    'a': 'lively',   # わいわい飲みたい
    'b': 'chill',    # しっとり飲みたい
    'c': 'silent',   # ひとりで静かに飲みたい
    'd': 'light',    # サクッと飲みたい
    'e': 'strong',   # がっつり飲みたい
}

# スコア計算の設定
SCORING_CONFIG = {
    'q2_multiplier': 1.5,     # q2は1.5倍
    'candidate_width': 5,      # 候補の幅
}

# デフォルトの重み（PHPから移植）
DEFAULT_WEIGHTS = {
    # A1: 初対面の場、あなたはどう動く？
    'A1:a': {'cocktail': 3, 'craft_beer': 3, 'wine_white': 2, 'sake_sweet': 1, 'shochu_mugi': 1},
    'A1:b': {'craft_beer': 2, 'sake_dry': 2, 'sake_sweet': 1, 'wine_white': 1, 'shochu_mugi': 1},
    'A1:c': {'sake_dry': 2, 'wine_red': 2, 'wine_white': 1, 'shochu_kome': 1},
    'A1:d': {'wine_red': 2, 'sake_dry': 2, 'shochu_kome': 1, 'shochu_mugi': 1, 'whisky': 1},
    'A1:e': {'wine_red': 3, 'whisky': 3, 'shochu_imo': 3},

    # A2: あなたは普段、どんな立ち位置？
    'A2:a': {'craft_beer': 3, 'wine_red': 2, 'sake_dry': 2, 'cocktail': 2, 'shochu_mugi': 1},
    'A2:b': {'cocktail': 3, 'craft_beer': 3, 'sake_sweet': 2, 'wine_white': 1, 'sake_dry': 1},
    'A2:c': {'wine_white': 2, 'shochu_mugi': 2, 'sake_dry': 1, 'sake_sweet': 1, 'shochu_kome': 1},
    'A2:d': {'shochu_kome': 3, 'sake_dry': 2, 'wine_red': 1, 'shochu_mugi': 1, 'whisky': 1},
    'A2:e': {'whisky': 3, 'wine_red': 3, 'shochu_imo': 3},

    # A3: 直感派？それとも理性派？
    'A3:a': {'cocktail': 3, 'wine_white': 2, 'craft_beer': 2, 'sake_sweet': 1, 'shochu_mugi': 1},
    'A3:b': {'craft_beer': 2, 'cocktail': 2, 'wine_white': 2, 'sake_dry': 1, 'sake_sweet': 1},
    'A3:c': {'sake_dry': 2, 'sake_sweet': 2, 'shochu_mugi': 2, 'wine_red': 1, 'wine_white': 1},
    'A3:d': {'sake_dry': 3, 'wine_red': 2, 'shochu_kome': 2, 'whisky': 1},
    'A3:e': {'sake_dry': 3, 'wine_red': 3, 'whisky': 3},

    # A4: 人にどう見られたい？
    'A4:a': {'cocktail': 3, 'craft_beer': 3, 'wine_white': 2, 'sake_sweet': 1},
    'A4:b': {'sake_sweet': 3, 'wine_white': 2, 'shochu_kome': 2, 'cocktail': 1},
    'A4:c': {'sake_dry': 3, 'wine_red': 3, 'craft_beer': 2, 'shochu_mugi': 1, 'whisky': 1},
    'A4:d': {'wine_red': 3, 'whisky': 3, 'shochu_kome': 2, 'sake_dry': 1},
    'A4:e': {'whisky': 3, 'shochu_imo': 3, 'wine_red': 2},

    # A5: あなたはストレス溜まると？
    'A5:a': {'craft_beer': 3, 'cocktail': 2, 'sake_dry': 1, 'wine_white': 1},
    'A5:b': {'craft_beer': 3, 'cocktail': 3, 'sake_sweet': 1, 'wine_white': 1},
    'A5:c': {'sake_dry': 2, 'wine_red': 2, 'shochu_mugi': 2, 'craft_beer': 1},
    'A5:d': {'wine_red': 3, 'whisky': 2, 'shochu_kome': 2, 'sake_dry': 1},
    'A5:e': {'whisky': 3, 'shochu_imo': 3, 'wine_red': 2},

    # A6: 自分の部屋の理想は？
    'A6:a': {'wine_white': 3, 'cocktail': 2, 'sake_sweet': 2, 'craft_beer': 1},
    'A6:b': {'sake_sweet': 2, 'shochu_kome': 2, 'sake_dry': 1, 'wine_white': 1},
    'A6:c': {'sake_dry': 1, 'sake_sweet': 1, 'shochu_mugi': 1},
    'A6:d': {'wine_red': 3, 'whisky': 2, 'shochu_kome': 2},
    'A6:e': {'whisky': 3, 'shochu_imo': 3, 'wine_red': 2},

    # A7: 落ち着くのは明るい？暗い？
    'A7:a': {'wine_white': 3, 'cocktail': 2, 'craft_beer': 2},
    'A7:b': {'wine_white': 2, 'craft_beer': 2, 'sake_sweet': 1},
    'A7:c': {'sake_dry': 1, 'shochu_mugi': 1, 'craft_beer': 1},
    'A7:d': {'wine_red': 3, 'shochu_imo': 2, 'whisky': 2},
    'A7:e': {'whisky': 3, 'shochu_imo': 3, 'wine_red': 3},

    # A8: 感情表現するときのタイプは？
    'A8:a': {'cocktail': 3, 'craft_beer': 2, 'wine_white': 2},
    'A8:b': {'craft_beer': 2, 'wine_white': 2, 'sake_sweet': 1},
    'A8:c': {'sake_dry': 1, 'sake_sweet': 1, 'shochu_mugi': 1},
    'A8:d': {'wine_red': 2, 'sake_dry': 2, 'shochu_kome': 2},
    'A8:e': {'whisky': 3, 'wine_red': 3, 'shochu_imo': 3},

    # A9: あなたにとってお酒はどんな関係？
    'A9:a': {'cocktail': 3, 'craft_beer': 2, 'wine_white': 1},
    'A9:b': {'sake_sweet': 3, 'shochu_kome': 2, 'wine_white': 1},
    'A9:c': {'sake_dry': 2, 'shochu_mugi': 2, 'craft_beer': 1},
    'A9:d': {'wine_red': 3, 'whisky': 2, 'sake_dry': 1},
    'A9:e': {'whisky': 3, 'shochu_imo': 3, 'wine_red': 2},

    # A10: あなたの判断タイプは？
    'A10:a': {'cocktail': 3, 'wine_white': 2, 'craft_beer': 1},
    'A10:b': {'wine_white': 2, 'craft_beer': 2, 'sake_sweet': 1},
    'A10:c': {'sake_dry': 2, 'shochu_mugi': 1, 'wine_white': 1},
    'A10:d': {'sake_dry': 3, 'wine_red': 2, 'whisky': 1},
    'A10:e': {'wine_red': 3, 'whisky': 3, 'shochu_imo': 2},

    # B1: どんなお酒が好き？
    'B1:a': {'sake_sweet': 5, 'cocktail': 5, 'wine_white': 4, 'shochu_mugi': 3},
    'B1:b': {'wine_white': 5, 'sake_sweet': 4, 'craft_beer': 4, 'wine_red': 3},
    'B1:c': {'wine_red': 5, 'shochu_imo': 5, 'whisky': 5, 'craft_beer': 4},
    'B1:d': {'sake_dry': 5, 'wine_white': 4, 'shochu_kome': 3},
    'B1:e': {'sake_dry': 4, 'sake_sweet': 4, 'shochu_mugi': 3, 'wine_white': 3},

    # B2: 普段飲むならどんな方向性？
    'B2:a': {'sake_sweet': 5, 'cocktail': 5, 'wine_white': 4},
    'B2:b': {'wine_white': 5, 'shochu_mugi': 4, 'sake_sweet': 3},
    'B2:c': {'sake_dry': 4, 'shochu_mugi': 4, 'craft_beer': 3},
    'B2:d': {'wine_red': 5, 'shochu_imo': 4, 'craft_beer': 4},
    'B2:e': {'wine_red': 5, 'shochu_imo': 5, 'whisky': 5},

    # B3: 飲み方のこだわりある？
    'B3:a': {'whisky': 5, 'shochu_imo': 4, 'wine_red': 3},
    'B3:b': {'shochu_kome': 5, 'shochu_mugi': 4, 'sake_sweet': 3},
    'B3:c': {'whisky': 5, 'shochu_imo': 4, 'wine_red': 4},
    'B3:d': {'cocktail': 5, 'craft_beer': 4, 'sake_sweet': 3},
    'B3:e': {'sake_dry': 3, 'sake_sweet': 3, 'shochu_mugi': 3, 'wine_white': 3},

    # B4: 料理と合わせるなら？
    'B4:a': {'sake_dry': 5, 'wine_white': 5, 'shochu_kome': 4},
    'B4:b': {'wine_red': 5, 'shochu_imo': 5, 'whisky': 4},
    'B4:c': {'shochu_mugi': 5, 'craft_beer': 4, 'sake_sweet': 3},
    'B4:d': {'wine_white': 5, 'sake_dry': 4, 'shochu_mugi': 3},
    'B4:e': {'wine_white': 5, 'wine_red': 4, 'craft_beer': 3},

    # B5: アルコール度数は？
    'B5:a': {'cocktail': 5, 'wine_white': 4, 'sake_sweet': 3},
    'B5:b': {'sake_sweet': 4, 'wine_white': 3, 'shochu_mugi': 3},
    'B5:c': {'sake_dry': 4, 'shochu_mugi': 3, 'wine_white': 3},
    'B5:d': {'wine_red': 5, 'shochu_imo': 4, 'craft_beer': 3},
    'B5:e': {'whisky': 5, 'shochu_imo': 5, 'wine_red': 4},

    # B6: 香りの好みは？
    'B6:a': {'wine_white': 5, 'sake_sweet': 4, 'cocktail': 4},
    'B6:b': {'sake_dry': 5, 'shochu_kome': 4, 'wine_white': 3},
    'B6:c': {'wine_white': 5, 'sake_sweet': 4, 'craft_beer': 3},
    'B6:d': {'wine_red': 5, 'shochu_imo': 4, 'shochu_mugi': 3},
    'B6:e': {'whisky': 5, 'shochu_imo': 5, 'wine_red': 4},

    # B7: ボディ感（重さ）は？
    'B7:a': {'wine_white': 5, 'sake_sweet': 4, 'shochu_mugi': 3},
    'B7:b': {'wine_white': 4, 'shochu_mugi': 3, 'sake_sweet': 3},
    'B7:c': {'sake_dry': 4, 'shochu_mugi': 3, 'craft_beer': 3},
    'B7:d': {'wine_red': 5, 'craft_beer': 4, 'shochu_imo': 3},
    'B7:e': {'wine_red': 5, 'shochu_imo': 5, 'whisky': 5},

    # B8: シーンで選ぶなら？
    'B8:a': {'craft_beer': 5, 'sake_dry': 4, 'shochu_mugi': 3},
    'B8:b': {'wine_white': 4, 'sake_sweet': 4, 'shochu_kome': 3},
    'B8:c': {'wine_red': 4, 'wine_white': 4, 'sake_dry': 3},
    'B8:d': {'shochu_mugi': 4, 'sake_sweet': 3, 'shochu_kome': 3},
    'B8:e': {'wine_red': 5, 'whisky': 5, 'shochu_imo': 4},

    # B9: 一本だけ選ぶなら？
    'B9:a': {'sake_sweet': 5, 'cocktail': 4, 'wine_white': 4},
    'B9:b': {'sake_dry': 5, 'wine_white': 3, 'shochu_kome': 3},
    'B9:c': {'shochu_mugi': 4, 'sake_sweet': 3, 'sake_dry': 3},
    'B9:d': {'shochu_imo': 5, 'craft_beer': 4, 'wine_red': 4},
    'B9:e': {'whisky': 5, 'wine_red': 5, 'shochu_imo': 4},

    # B10: 最後に決め手になるのは？
    'B10:a': {'wine_white': 5, 'wine_red': 4, 'sake_sweet': 3},
    'B10:b': {'sake_sweet': 4, 'sake_dry': 4, 'shochu_mugi': 3},
    'B10:c': {'whisky': 5, 'wine_red': 4, 'shochu_imo': 4},
    'B10:d': {'shochu_mugi': 4, 'sake_dry': 3, 'sake_sweet': 3},
    'B10:e': {'cocktail': 5, 'wine_white': 4, 'craft_beer': 4},

    # C1: 今日はどんな雰囲気で飲みたい？
    'C1:a': {'craft_beer': 5, 'cocktail': 4, 'wine_white': 3},
    'C1:b': {'sake_sweet': 3, 'wine_white': 3, 'shochu_mugi': 2},
    'C1:c': {'wine_red': 4, 'shochu_kome': 3, 'sake_dry': 2},
    'C1:d': {'whisky': 4, 'wine_red': 3, 'shochu_imo': 3},
    'C1:e': {'whisky': 5, 'shochu_imo': 4, 'wine_red': 4},

    # C2: 新しいものにチャレンジしたい？
    'C2:a': {'craft_beer': 5, 'cocktail': 4, 'wine_white': 3},
    'C2:b': {'craft_beer': 4, 'wine_white': 3, 'sake_sweet': 2},
    'C2:c': {'sake_dry': 2, 'sake_sweet': 2, 'shochu_mugi': 2},
    'C2:d': {'wine_red': 3, 'shochu_kome': 2, 'whisky': 2},
    'C2:e': {'wine_red': 4, 'shochu_imo': 3, 'whisky': 3},

    # C3: 飲む目的はどれに近い？
    'C3:a': {'craft_beer': 4, 'cocktail': 3, 'wine_white': 2},
    'C3:b': {'sake_sweet': 4, 'wine_white': 3, 'shochu_kome': 2},
    'C3:c': {'wine_red': 4, 'craft_beer': 3, 'sake_dry': 2},
    'C3:d': {'whisky': 4, 'wine_red': 3, 'shochu_imo': 2},
    'C3:e': {'whisky': 5, 'shochu_imo': 4, 'wine_red': 3},

    # C4: テンションの高さは今どれくらい？
    'C4:a': {'craft_beer': 5, 'cocktail': 5, 'wine_white': 3},
    'C4:b': {'craft_beer': 4, 'cocktail': 4, 'sake_sweet': 3},
    'C4:c': {'sake_dry': 2, 'shochu_mugi': 2, 'wine_white': 1},
    'C4:d': {'wine_red': 3, 'shochu_kome': 2, 'sake_dry': 2},
    'C4:e': {'whisky': 4, 'shochu_imo': 3, 'wine_red': 3},

    # C5: 今日はどこに座りたい？
    'C5:a': {'craft_beer': 4, 'sake_dry': 3, 'cocktail': 2},
    'C5:b': {'sake_sweet': 3, 'wine_white': 2, 'shochu_mugi': 2},
    'C5:c': {'craft_beer': 3, 'wine_white': 2, 'sake_dry': 2},
    'C5:d': {'wine_red': 4, 'shochu_kome': 3, 'whisky': 3},
    'C5:e': {'whisky': 5, 'wine_red': 4, 'shochu_imo': 4},

    # C6: エネルギー残量はどれくらい？
    'C6:a': {'craft_beer': 5, 'cocktail': 4, 'wine_white': 3},
    'C6:b': {'craft_beer': 4, 'sake_sweet': 3, 'wine_white': 2},
    'C6:c': {'sake_dry': 3, 'shochu_mugi': 2, 'wine_white': 1},
    'C6:d': {'wine_red': 3, 'shochu_kome': 2, 'sake_dry': 2},
    'C6:e': {'whisky': 4, 'shochu_imo': 4, 'wine_red': 3},

    # C7: 飲みの目的は？
    'C7:a': {'craft_beer': 5, 'cocktail': 4, 'wine_white': 3},
    'C7:b': {'sake_sweet': 4, 'wine_white': 3, 'shochu_kome': 2},
    'C7:c': {'sake_dry': 3, 'craft_beer': 3, 'wine_white': 2},
    'C7:d': {'wine_red': 4, 'shochu_imo': 3, 'whisky': 3},
    'C7:e': {'whisky': 5, 'shochu_imo': 4, 'wine_red': 3},

    # C8: 今の気持ちを一言で言うなら？
    'C8:a': {'craft_beer': 5, 'cocktail': 4},
    'C8:b': {'sake_sweet': 3, 'wine_white': 2},
    'C8:c': {'sake_dry': 2, 'shochu_mugi': 2},
    'C8:d': {'wine_red': 3, 'shochu_kome': 2},
    'C8:e': {'whisky': 5, 'shochu_imo': 4},

    # C9: 今日のご褒美に選ぶなら？
    'C9:a': {'sake_sweet': 5, 'cocktail': 4},
    'C9:b': {'wine_red': 5, 'shochu_imo': 4, 'whisky': 3},
    'C9:c': {'sake_dry': 3, 'shochu_mugi': 2},
    'C9:d': {'craft_beer': 4, 'wine_white': 3},
    'C9:e': {'wine_white': 4, 'sake_sweet': 3},

    # C10: 話しやすいと言われる？
    'C10:a': {'cocktail': 4, 'craft_beer': 4, 'wine_white': 2},
    'C10:b': {'sake_sweet': 3, 'wine_white': 2},
    'C10:c': {'sake_dry': 2, 'shochu_mugi': 2},
    'C10:d': {'wine_red': 3, 'shochu_kome': 2},
    'C10:e': {'whisky': 5, 'shochu_imo': 4, 'wine_red': 3},

    # q2（1.5倍適用対象）
    'q2:a': {'sake_sweet': 6, 'wine_white': 5, 'cocktail': 5, 'shochu_mugi': 3},
    'q2:b': {'wine_white': 6, 'wine_red': 5, 'sake_sweet': 4, 'craft_beer': 3},
    'q2:c': {'wine_red': 6, 'shochu_imo': 6, 'whisky': 6, 'craft_beer': 4},
    'q2:d': {'sake_dry': 6, 'wine_white': 5, 'shochu_kome': 4},
    'q2:e': {'sake_dry': 5, 'sake_sweet': 5, 'shochu_mugi': 4, 'wine_white': 3},
}
