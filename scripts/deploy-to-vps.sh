#!/usr/bin/env bash
# 5akeMe 本番デプロイ（ローカル側）
# 使い方: リポジトリルートで ./scripts/deploy-to-vps.sh
# 事前: ssh ryoya@160.251.214.119 で鍵ログインできること

set -e
VPS="ryoya@160.251.214.119"
REMOTE_DIR="~/5akeme"
REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

cd "$REPO_ROOT"

echo "==> 1. 事前チェック"
if [ ! -f api/vendor/autoload.php ]; then
  echo "    エラー: api/vendor がありません。先に cd api && composer install を実行してください。"
  exit 1
fi

echo ""
echo "==> 2. フロントビルド（api/public/build）"
if [ ! -f api/public/build/manifest.json ]; then
  (cd api && npm ci && npm run build)
else
  echo "    manifest.json あり。再ビルドする場合は: cd api && npm run build"
  read -r -p "    再ビルドしますか? [y/N] " q
  if [ "$q" = "y" ] || [ "$q" = "Y" ]; then
    (cd api && npm run build)
  fi
fi

echo ""
echo "==> 3. サーバへ rsync（.git / node_modules / .env / hot / サーバ生成の storage は送らない）"
rsync -avz --no-perms --no-owner --no-group --no-times \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='.env' \
  --exclude='.env.*' \
  --exclude='api/public/hot' \
  --exclude='api/storage/framework/views/' \
  --exclude='api/storage/framework/sessions/' \
  --exclude='api/storage/framework/cache/' \
  --exclude='api/storage/logs/' \
  "$REPO_ROOT/" "$VPS:$REMOTE_DIR/"

echo ""
echo "==> 4. サーバで実行するコマンド（コピペ用）"
echo "----------------------------------------"
cat << 'SERVER_CMDS'
ssh ryoya@160.251.214.119

cd ~/5akeme

# 初回のみ: .env を用意
# 方法1) ローカルで .env を作ってある場合: ./scripts/send-env-to-vps.sh で送る
# 方法2) サーバで作成: cp api/.env.example api/.env && cp .env.example .env && nano api/.env && nano .env

docker compose -f docker-compose.production.yml build
docker compose -f docker-compose.production.yml up -d

# APP_KEY 未生成なら
docker compose -f docker-compose.production.yml exec app php artisan key:generate
docker compose -f docker-compose.production.yml exec app php artisan config:cache

# マイグレーション・ストレージ・キャッシュ
docker compose -f docker-compose.production.yml exec app php artisan migrate --force
docker compose -f docker-compose.production.yml exec app php artisan storage:link
docker compose -f docker-compose.production.yml exec app php artisan config:cache
docker compose -f docker-compose.production.yml exec app php artisan view:cache
SERVER_CMDS
echo "----------------------------------------"
echo "ブラウザで http://160.251.214.119 を開いて確認してください。"
