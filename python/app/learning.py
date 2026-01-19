"""
5akeMe 学習エンジン

ユーザーのフィードバックから重みを調整する簡易AIロジック
"""

import json
import os
from typing import Dict, List, Any
from copy import deepcopy
from datetime import datetime

from app.config import DEFAULT_WEIGHTS, SAKE_TYPES

# 学習履歴ファイル
LEARNING_HISTORY_FILE = "/app/data/learning_history.json"
LEARNED_WEIGHTS_FILE = "/app/data/learned_weights.json"


class FeedbackLearner:
    """
    フィードバックから学習するクラス
    
    学習ロジック:
    - 高評価(4-5点): その回答パターンで得られた結果タイプの重みを強化
    - 低評価(1-2点): その回答パターンで得られた結果タイプの重みを弱める
    - 普通(3点): 変化なし
    
    シンプルな重み調整アルゴリズム:
    - 高評価: 関連する重みを +0.1 〜 +0.3（評価に応じて）
    - 低評価: 関連する重みを -0.1 〜 -0.2（評価に応じて）
    """
    
    def __init__(self):
        self.history = self._load_history()
        self.weights = self._load_weights()
    
    def _load_history(self) -> List[Dict]:
        """学習履歴を読み込む"""
        if os.path.exists(LEARNING_HISTORY_FILE):
            try:
                with open(LEARNING_HISTORY_FILE, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except Exception:
                pass
        return []
    
    def _save_history(self):
        """学習履歴を保存"""
        os.makedirs(os.path.dirname(LEARNING_HISTORY_FILE), exist_ok=True)
        with open(LEARNING_HISTORY_FILE, 'w', encoding='utf-8') as f:
            json.dump(self.history, f, ensure_ascii=False, indent=2)
    
    def _load_weights(self) -> Dict[str, Dict[str, float]]:
        """学習済み重みを読み込む"""
        if os.path.exists(LEARNED_WEIGHTS_FILE):
            try:
                with open(LEARNED_WEIGHTS_FILE, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except Exception:
                pass
        return deepcopy(DEFAULT_WEIGHTS)
    
    def _save_weights(self):
        """学習済み重みを保存"""
        os.makedirs(os.path.dirname(LEARNED_WEIGHTS_FILE), exist_ok=True)
        with open(LEARNED_WEIGHTS_FILE, 'w', encoding='utf-8') as f:
            json.dump(self.weights, f, ensure_ascii=False, indent=2)
    
    def learn(self, feedbacks: List[Any]) -> int:
        """
        フィードバックから学習する
        
        Args:
            feedbacks: [
                {
                    "answers": {"q1": "a", "q2": "c", ...},
                    "result_type": "sake_dry",
                    "mood": "lively",
                    "rating": 5
                },
                ...
            ]
        
        Returns:
            学習したフィードバック数
        """
        updated_count = 0
        
        for feedback in feedbacks:
            answers = feedback.answers if hasattr(feedback, 'answers') else feedback.get('answers', {})
            result_type = feedback.result_type if hasattr(feedback, 'result_type') else feedback.get('result_type')
            rating = feedback.rating if hasattr(feedback, 'rating') else feedback.get('rating', 3)
            
            if not answers or not result_type:
                continue
            
            # 学習率を決定
            if rating >= 4:
                # 高評価: 正の学習
                learning_rate = 0.1 * (rating - 3)  # 4点: 0.1, 5点: 0.2
                direction = 1
            elif rating <= 2:
                # 低評価: 負の学習
                learning_rate = 0.1 * (3 - rating)  # 2点: 0.1, 1点: 0.2
                direction = -1
            else:
                # 普通: 学習しない
                continue
            
            # 回答パターンに基づいて重みを調整
            for q_key, choice in answers.items():
                map_key = f"{q_key}:{choice}"
                
                if map_key not in self.weights:
                    continue
                
                # この回答で結果タイプに加点されている場合、その重みを調整
                if result_type in self.weights[map_key]:
                    current_weight = self.weights[map_key][result_type]
                    adjustment = learning_rate * direction
                    
                    # 重みの下限を0.5、上限を10に制限
                    new_weight = max(0.5, min(10, current_weight + adjustment))
                    self.weights[map_key][result_type] = round(new_weight, 2)
            
            # 履歴に追加
            self.history.append({
                'timestamp': datetime.now().isoformat(),
                'answers': answers,
                'result_type': result_type,
                'rating': rating,
                'learning_rate': learning_rate,
                'direction': direction
            })
            
            updated_count += 1
        
        # 保存
        if updated_count > 0:
            self._save_weights()
            self._save_history()
        
        return updated_count
    
    def get_current_weights(self) -> Dict[str, Dict[str, float]]:
        """現在の学習済み重みを取得"""
        return self.weights
    
    def get_total_learned(self) -> int:
        """累計学習数を取得"""
        return len(self.history)
    
    def get_learning_stats(self) -> Dict[str, Any]:
        """学習統計を取得"""
        if not self.history:
            return {
                'total': 0,
                'positive': 0,
                'negative': 0,
                'by_result_type': {}
            }
        
        positive = sum(1 for h in self.history if h.get('direction', 0) > 0)
        negative = sum(1 for h in self.history if h.get('direction', 0) < 0)
        
        by_type = {}
        for h in self.history:
            rt = h.get('result_type', 'unknown')
            if rt not in by_type:
                by_type[rt] = {'positive': 0, 'negative': 0}
            if h.get('direction', 0) > 0:
                by_type[rt]['positive'] += 1
            elif h.get('direction', 0) < 0:
                by_type[rt]['negative'] += 1
        
        return {
            'total': len(self.history),
            'positive': positive,
            'negative': negative,
            'by_result_type': by_type
        }
    
    def reset(self):
        """学習履歴と重みをリセット"""
        self.history = []
        self.weights = deepcopy(DEFAULT_WEIGHTS)
        self._save_history()
        self._save_weights()
