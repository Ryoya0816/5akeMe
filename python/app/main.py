"""
5akeMe - Python AI スコア計算サービス

ユーザーのフィードバックを元に、診断スコア計算の重みを学習・調整する
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Dict, List, Optional
import json
import os

from app.scoring import SakeScorer
from app.learning import FeedbackLearner

app = FastAPI(
    title="5akeMe AI Service",
    description="日本酒診断のスコア計算と学習を行うAIサービス",
    version="1.0.0"
)

# CORS設定（Laravelからのリクエストを許可）
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:8082", "http://127.0.0.1:8082"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# サービスのインスタンス
scorer = SakeScorer()
learner = FeedbackLearner()


# ========================================
# リクエスト/レスポンスモデル
# ========================================

class ScoreRequest(BaseModel):
    """スコア計算リクエスト"""
    answers: Dict[str, str]
    seed: Optional[int] = None


class ScoreResponse(BaseModel):
    """スコア計算レスポンス"""
    primary: str
    primary_label: str
    candidates: List[Dict]
    top5: List[Dict]
    mood: str
    scores: Dict[str, float]


class FeedbackData(BaseModel):
    """フィードバックデータ"""
    answers: Dict[str, str]
    result_type: str
    mood: str
    rating: int  # 1-5


class LearnRequest(BaseModel):
    """学習リクエスト"""
    feedbacks: List[FeedbackData]


class HealthResponse(BaseModel):
    """ヘルスチェックレスポンス"""
    status: str
    model_version: str
    total_feedbacks_learned: int


# ========================================
# エンドポイント
# ========================================

@app.get("/", response_model=HealthResponse)
async def root():
    """ヘルスチェック & ステータス確認"""
    return HealthResponse(
        status="ok",
        model_version=scorer.get_version(),
        total_feedbacks_learned=learner.get_total_learned()
    )


@app.get("/health")
async def health_check():
    """簡易ヘルスチェック"""
    return {"status": "ok"}


@app.post("/score", response_model=ScoreResponse)
async def calculate_score(request: ScoreRequest):
    """
    診断スコアを計算する
    
    - answers: {"q1": "lively", "q2": "light", "A1": "yes", ...}
    - seed: 乱数シード（省略可）
    """
    try:
        result = scorer.calculate(request.answers, request.seed)
        return ScoreResponse(**result)
    except Exception as e:
        raise HTTPException(status_code=422, detail=str(e))


@app.post("/learn")
async def learn_from_feedback(request: LearnRequest):
    """
    フィードバックデータから学習する
    
    高評価(4-5点)の回答パターン → その結果タイプの重みを強化
    低評価(1-2点)の回答パターン → その結果タイプの重みを弱める
    """
    try:
        updated_count = learner.learn(request.feedbacks)
        
        # 学習後の重みをスコアラーに反映
        new_weights = learner.get_current_weights()
        scorer.update_weights(new_weights)
        
        return {
            "success": True,
            "message": f"{updated_count}件のフィードバックから学習しました",
            "total_learned": learner.get_total_learned()
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@app.get("/weights")
async def get_current_weights():
    """現在の重み設定を取得（デバッグ用）"""
    return {
        "weights": scorer.get_weights(),
        "version": scorer.get_version()
    }


@app.post("/weights/reset")
async def reset_weights():
    """重みを初期値にリセット"""
    scorer.reset_weights()
    learner.reset()
    return {
        "success": True,
        "message": "重みを初期値にリセットしました"
    }
