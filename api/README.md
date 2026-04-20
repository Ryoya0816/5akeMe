# 5akeMe
診断データから“好み”を推定し、レコメンドにつなげるプロトタイプ。

## 概要
5akeMe は、数問の診断回答から嗜好タイプを推定し、結果（primary / candidates / mood）を表示する Web アプリです。  
将来的には結果から「おすすめのお酒・お店」へ誘導することで、レコメンド体験として完成させる想定です。

## 想定ユースケース（企業向け）
- 診断（アンケート）結果をもとに、商品/店舗/コンテンツをレコメンドしたい
- キャンペーン LP の導線強化（診断 → 結果 → 回遊）
- 小規模データから開始し、後に DB 連携・ABテスト・レコメンド精度改善へ拡張

## デモ / スクリーンショット
- Top：暖簾アニメーション + Welcome ボタン（調整中）
- Result：タイプ（primary）+ 候補（candidates）+ mood

> ※ここにGIFやスクショを貼ると、GitHub 上で一瞬で伝わります

## 主な機能
- 診断セッション生成（固定2問 + カテゴリA/B/Cから各1問）
- スコア計算（10種類の酒タイプに加点、q2のみ倍率適用）
- 結果出力：primary / candidates / mood
- UI：トップ演出（暖簾・Welcomeボタンなど）※調整中
- Result → お店導線（ダミー導線 → 実装予定）

## 技術スタック
- Backend: Laravel
- Frontend: Blade（主体） + JavaScript（演出/補助）
- Styling: Tailwind CSS + `resources/css/app.css`（共通トークン管理）
- Infra: Docker Compose
- DB: MySQL（想定）

## アーキテクチャ概要（診断ロジック）
- `app/Services/DiagnoseService.php`
  - `createSession`：固定2問（q1,q2）＋カテゴリA/B/Cから各1問を生成
  - `score`：`config/diagnose.php` の types/labels/weights/scoring を参照して加点
  - q2のみ `q2_multiplier` を適用
  - q1回答(a〜e)から mood（lively/chill/silent/light/strong）を決定
  - `candidate_width` 以内のタイプを候補化し降順ソート
  - return：primary / candidates / mood

## セットアップ（ローカル開発）
### 前提
- Docker / Docker Compose
- Node.js（Viteビルドを使う場合）

### 起動（Backend）
```bash
docker compose up -d
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan migrate
