#!/usr/bin/env bash
# 5akeMe 本番デプロイ（コード + DB をローカル Docker からそのまま反映）
# 使い方: リポジトリルートで ./scripts/deploy-with-db.sh [-y]
#   -y : 非対話モード（再ビルドの確認をスキップ）
# 事前: ssh ryoya@160.251.214.119 で鍵ログインできること
# 事前: ローカルで docker compose up -d しておくこと（DB をダンプするため）

set -e
VPS="ryoya@160.251.214.119"
REMOTE_DIR="~/5akeme"
REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DUMP_FILE="db_dump.sql"
NON_INTERACTIVE=false
[ "${1:-}" = "-y" ] && NON_INTERACTIVE=true

cd "$REPO_ROOT"

echo "==> 1. 事前チェック"
# ローカル DB
if ! docker compose ps db 2>/dev/null | grep -q "Up"; then
  echo "    エラー: ローカル DB が起動していません。先に docker compose up -d を実行してください。"
  exit 1
fi
# vendor（Composer 依存）
if [ ! -f api/vendor/autoload.php ]; then
  echo "    エラー: api/vendor がありません。先に cd api && composer install を実行してください。"
  exit 1
fi

echo ""
echo "==> 2. フロントビルド（api/public/build）"
if [ ! -f api/public/build/manifest.json ]; then
  (cd api && npm ci && npm run build)
else
  echo "    manifest.json あり"
  if [ "$NON_INTERACTIVE" = false ]; then
    read -r -p "    再ビルドしますか? [y/N] " q
    if [ "$q" = "y" ] || [ "$q" = "Y" ]; then
      (cd api && npm run build)
    fi
  fi
fi

echo ""
echo "==> 3. サーバへ rsync（コード）"
rsync -avz \
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
echo "==> 4. ローカル DB をダンプ"
docker compose exec -T db mysqldump -u laravel -plaravel --no-tablespaces laravel > "$DUMP_FILE"
if [ ! -s "$DUMP_FILE" ]; then
  echo "    エラー: ダンプが空です。DB に接続できているか確認してください。"
  rm -f "$DUMP_FILE"
  exit 1
fi
echo "    ダンプ完了: $(wc -l < "$DUMP_FILE") 行"

echo ""
echo "==> 5. ダンプを VPS に転送"
scp "$DUMP_FILE" "$VPS:~/"
rm -f "$DUMP_FILE"

echo ""
echo "==> 6. VPS で本番 DB にインポート（ローカル DB で上書き）"
ssh "$VPS" 'cd ~/5akeme && \
  docker compose -f docker-compose.production.yml up -d && \
  DB_PASS=$(grep "^DB_PASSWORD=" .env 2>/dev/null | cut -d= -f2-) && \
  if [ -z "$DB_PASS" ]; then echo "エラー: ~/5akeme/.env に DB_PASSWORD が設定されていません"; exit 1; fi && \
  IMPORTED=false && \
  for i in 1 2 3 4 5 6 7 8 9 10; do \
    if cat ~/db_dump.sql | docker compose -f docker-compose.production.yml exec -T db mysql -u laravel -p"$DB_PASS" laravel 2>/dev/null; then \
      IMPORTED=true; break; \
    fi; \
    echo "    MySQL 起動待機... ($i/10)"; sleep 3; \
  done && \
  rm -f ~/db_dump.sql && \
  if [ "$IMPORTED" = false ]; then echo "エラー: DB インポートに失敗しました"; exit 1; fi && \
  echo "インポート完了"'

echo ""
echo "==> 7. VPS でコンテナ再起動・キャッシュクリア"
ssh "$VPS" 'cd ~/5akeme && \
  docker compose -f docker-compose.production.yml up -d --build && \
  docker compose -f docker-compose.production.yml exec -T app php artisan storage:link 2>/dev/null || true && \
  docker compose -f docker-compose.production.yml exec -T app php artisan config:cache && \
  docker compose -f docker-compose.production.yml exec -T app php artisan view:cache && \
  rm -f api/public/hot'

echo ""
echo "==> 完了"
echo "ブラウザで http://160.251.214.119 を開いて確認してください。"
echo ""
echo "※ ローカル Docker の DB がそのまま本番に反映されました。"
echo "※ 本番にしかないデータは上書きで消えています。"
