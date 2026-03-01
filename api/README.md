# 5akeMe API（Laravel）

診断データから「好み」を推定し、レコメンドにつなげるプロトタイプのバックエンドです。

---

## クイックスタート

### 前提

- Docker / Docker Compose
- （フロント資産のビルド時）Node.js

### 起動

```bash
# リポジトリルートで
docker compose up -d
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan migrate
```

ブラウザで **http://localhost:8082** を開きます。

### ローカルでログインするには

メール/パスワードでログインするには、テスト用ユーザーを1件作成します。

```bash
docker compose exec app php artisan user:create-hello
```

表示された **メールアドレス** と **パスワード** で [http://localhost:8082/login](http://localhost:8082/login) にログインできます。

**ログイン直後にログアウトされる場合**  
`api/.env` の `SESSION_DRIVER=file` を `SESSION_DRIVER=database` に変更し、`docker compose exec app php artisan config:clear` を実行してから再度お試しください。

### フロント資産のビルド

```bash
cd api && npm install && npm run build
```

ビューキャッシュを消す場合: `docker compose exec app php artisan view:clear`（Docker 内で実行する場合）。

---

## 主な機能

- **診断**: 固定2問（q1 気分 / q2 お酒に求めるもの）+ カテゴリ A/B/C から各1問 → 計5問。10種類の酒タイプにスコア付けし、primary / candidates / mood を出力。
- **認証**: メール・パスワード + SNS（Google / LINE / X）。SNS は `SNS_LOGIN_ENABLED` で有効化。
- **店舗・通報・フィードバック**: 診断結果への評価（1〜5点）を保存。精度改善の分析に利用可能。
- **管理画面**: Filament（ユーザー・店舗・通報・診断フィードバックの一覧・編集）。

---

## 技術スタック

- **Backend**: PHP 8.x / Laravel
- **Frontend**: Blade + Vite + Tailwind CSS + Alpine.js
- **DB**: MySQL 8
- **Infra**: Docker Compose（web / app / db / python 等）

詳細は [../docs/TECH_STACK.md](../docs/TECH_STACK.md) を参照。

---

## 診断ロジック（概要）

- **設定**: `config/diagnose.php`（types / labels / weights / scoring / 固定質問・カテゴリ質問）
- **サービス**: `app/Services/DiagnoseService.php`
  - `createSession()`: 固定2問 + カテゴリ A/B/C から各1問を選んでセッション生成
  - `score()`: weights に従ってタイプ別に加点。q2 は `q2_multiplier` 適用。最大スコアが primary、差が `candidate_width` 以内が candidates。q1 から mood を決定。
- **精度改善**: フィードバック集計は `php artisan diagnose:feedback-stats`。方針は [../docs/DIAGNOSE_ACCURACY.md](../docs/DIAGNOSE_ACCURACY.md)。

---

## ドキュメント（docs/）

| ファイル | 内容 |
|----------|------|
| [TECH_STACK.md](../docs/TECH_STACK.md) | 技術スタック・コンテナ構成 |
| [DIAGNOSE_ACCURACY.md](../docs/DIAGNOSE_ACCURACY.md) | 診断精度の改善方針・コマンド |
| [SNS_LOGIN.md](../docs/SNS_LOGIN.md) | SNS ログインの設定手順 |
| [README.md](../docs/README.md) | DB スキーマ・ER 図の生成（tbls） |
