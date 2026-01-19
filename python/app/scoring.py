"""
5akeMe スコア計算エンジン

フィードバックで学習した重みを使ってスコアを計算する
"""

import json
import os
from typing import Dict, List, Optional, Any
from copy import deepcopy

from app.config import (
    SAKE_TYPES,
    LABELS,
    MOOD_MAP,
    SCORING_CONFIG,
    DEFAULT_WEIGHTS
)

# 重みファイルのパス
WEIGHTS_FILE = "/app/data/learned_weights.json"
VERSION_FILE = "/app/data/model_version.txt"


class SakeScorer:
    """
    診断スコアを計算するクラス
    
    - 回答パターンから各お酒タイプのスコアを計算
    - 学習した重みで調整可能
    """
    
    def __init__(self):
        self.weights = self._load_weights()
        self.version = self._load_version()
    
    def _load_weights(self) -> Dict[str, Dict[str, float]]:
        """学習済み重みをファイルから読み込む（なければデフォルト）"""
        if os.path.exists(WEIGHTS_FILE):
            try:
                with open(WEIGHTS_FILE, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except Exception:
                pass
        return deepcopy(DEFAULT_WEIGHTS)
    
    def _load_version(self) -> str:
        """モデルバージョンを読み込む"""
        if os.path.exists(VERSION_FILE):
            try:
                with open(VERSION_FILE, 'r') as f:
                    return f.read().strip()
            except Exception:
                pass
        return "1.0.0"
    
    def _save_weights(self):
        """重みをファイルに保存"""
        os.makedirs(os.path.dirname(WEIGHTS_FILE), exist_ok=True)
        with open(WEIGHTS_FILE, 'w', encoding='utf-8') as f:
            json.dump(self.weights, f, ensure_ascii=False, indent=2)
    
    def _save_version(self, version: str):
        """バージョンをファイルに保存"""
        os.makedirs(os.path.dirname(VERSION_FILE), exist_ok=True)
        with open(VERSION_FILE, 'w') as f:
            f.write(version)
        self.version = version
    
    def calculate(self, answers: Dict[str, str], seed: Optional[int] = None) -> Dict[str, Any]:
        """
        スコアを計算する
        
        Args:
            answers: {"q1": "a", "q2": "c", "A1": "b", ...}
            seed: 乱数シード（現在は未使用）
        
        Returns:
            {
                "primary": "sake_dry",
                "primary_label": "日本酒・辛口",
                "candidates": [...],
                "top5": [...],
                "mood": "lively",
                "scores": {...}
            }
        """
        # 初期スコア
        scores = {sake_type: 0.0 for sake_type in SAKE_TYPES}
        
        # mood: q1の回答から取得
        mood = None
        if 'q1' in answers:
            mood = MOOD_MAP.get(answers['q1'])
        
        # スコア加点
        q2_multiplier = SCORING_CONFIG['q2_multiplier']
        
        for q_key, choice in answers.items():
            map_key = f"{q_key}:{choice}"
            
            if map_key not in self.weights:
                continue
            
            # q2だけ倍率をかける
            factor = q2_multiplier if q_key == 'q2' else 1.0
            
            # 加点
            for sake_type, point in self.weights[map_key].items():
                if sake_type in scores:
                    scores[sake_type] += point * factor
        
        # スコア降順でソート
        sorted_scores = sorted(scores.items(), key=lambda x: x[1], reverse=True)
        
        # 最大スコア
        max_score = sorted_scores[0][1] if sorted_scores else 0.0
        
        # 候補抽出（最大スコアとの差がcandidate_width以内）
        candidate_width = SCORING_CONFIG['candidate_width']
        candidates = []
        for sake_type, score in sorted_scores:
            if max_score - score <= candidate_width:
                candidates.append({
                    'type': sake_type,
                    'score': round(score, 2),
                    'label': LABELS.get(sake_type, sake_type)
                })
        
        # primary（1位）
        primary = candidates[0]['type'] if candidates else SAKE_TYPES[0]
        primary_label = LABELS.get(primary, primary)
        
        # top5（チャート用）
        top5 = []
        for sake_type, score in sorted_scores[:5]:
            top5.append({
                'type': sake_type,
                'score': round(score, 2),
                'label': LABELS.get(sake_type, sake_type)
            })
        
        # top5が5未満なら補充
        if len(top5) < 5:
            top5_types = {item['type'] for item in top5}
            priority_types = ['sake_dry', 'sake_sweet', 'shochu_kome', 'wine_white']
            
            for sake_type in priority_types:
                if len(top5) >= 5:
                    break
                if sake_type not in top5_types:
                    top5.append({
                        'type': sake_type,
                        'score': 0.0,
                        'label': LABELS.get(sake_type, sake_type)
                    })
        
        return {
            'primary': primary,
            'primary_label': primary_label,
            'candidates': candidates,
            'top5': top5,
            'mood': mood,
            'scores': {k: round(v, 2) for k, v in scores.items()}
        }
    
    def update_weights(self, new_weights: Dict[str, Dict[str, float]]):
        """重みを更新"""
        self.weights = new_weights
        self._save_weights()
        
        # バージョン更新
        current_parts = self.version.split('.')
        minor = int(current_parts[1]) + 1
        new_version = f"{current_parts[0]}.{minor}.0"
        self._save_version(new_version)
    
    def get_weights(self) -> Dict[str, Dict[str, float]]:
        """現在の重みを取得"""
        return self.weights
    
    def get_version(self) -> str:
        """現在のバージョンを取得"""
        return self.version
    
    def reset_weights(self):
        """重みを初期値にリセット"""
        self.weights = deepcopy(DEFAULT_WEIGHTS)
        self._save_weights()
        self._save_version("1.0.0")
