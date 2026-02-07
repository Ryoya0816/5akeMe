#!/usr/bin/env bash
# ローカルの .env を VPS に送るだけ（deploy-to-vps.sh では .env は送らない）
# 使い方: リポジトリルートで ./scripts/send-env-to-vps.sh
# 事前: api/.env と .env が存在し、DB_PASSWORD を設定済みであること

set -e
VPS="ryoya@160.251.214.119"
REMOTE_DIR="~/5akeme"
REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

cd "$REPO_ROOT"
[ -f api/.env ] || { echo "api/.env がありません"; exit 1; }
[ -f .env ] || { echo ".env（ルート）がありません"; exit 1; }

echo "==> .env を VPS に送信"
scp api/.env "$VPS:$REMOTE_DIR/api/"
scp .env "$VPS:$REMOTE_DIR/"
echo "完了。VPS で docker compose を起動してください。"
