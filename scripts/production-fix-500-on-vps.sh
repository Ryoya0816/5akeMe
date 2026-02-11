#!/usr/bin/env bash
# VPS 上で実行: 本番 500 を一括で直す（DB設定・セッション・キャッシュ・ビルド前提の確認）
# 使い方: scp で送ってから ssh で実行
#   scp scripts/production-fix-500-on-vps.sh ryoya@160.251.214.119:~/5akeme/
#   ssh ryoya@160.251.214.119 'cd ~/5akeme && bash production-fix-500-on-vps.sh'

set -e
cd ~/5akeme || { echo "エラー: ~/5akeme がありません"; exit 1; }

API_ENV="api/.env"
ROOT_ENV=".env"
COMPOSE="docker compose -f docker-compose.production.yml"

echo "=========================================="
echo "  本番 500 対策スクリプト（1本で実行）"
echo "=========================================="
echo ""

# --- 1. DB 設定（api/.env）---
echo "==> 1. DB 設定（api/.env）"
if [ ! -f "$API_ENV" ]; then
  echo "    エラー: $API_ENV がありません。デプロイまたは scp で api/.env を配置してください。"
  exit 1
fi
grep -q '^DB_USERNAME=' "$API_ENV" && sed -i 's/^DB_USERNAME=.*/DB_USERNAME=laravel/' "$API_ENV" || echo 'DB_USERNAME=laravel' >> "$API_ENV"
grep -q '^DB_HOST=' "$API_ENV" && sed -i 's/^DB_HOST=.*/DB_HOST=db/' "$API_ENV" || echo 'DB_HOST=db' >> "$API_ENV"
if [ -f "$ROOT_ENV" ]; then
  ROOT_PASS=$(grep '^DB_PASSWORD=' "$ROOT_ENV" 2>/dev/null | cut -d= -f2-)
  if [ -n "$ROOT_PASS" ]; then
    export ROOT_PASS
    if grep -q '^DB_PASSWORD=' "$API_ENV"; then
      awk 'BEGIN{pass=ENVIRON["ROOT_PASS"]} /^DB_PASSWORD=/{print "DB_PASSWORD=" pass; next} {print}' "$API_ENV" > "$API_ENV.tmp" && mv "$API_ENV.tmp" "$API_ENV"
    else
      awk 'BEGIN{pass=ENVIRON["ROOT_PASS"]} {print} END{print "DB_PASSWORD=" pass}' "$API_ENV" > "$API_ENV.tmp" && mv "$API_ENV.tmp" "$API_ENV"
    fi
    echo "    DB_PASSWORD をルート .env と同期しました。"
  fi
fi
echo "    DB_USERNAME / DB_HOST / DB_PASSWORD 確認済み。"
echo ""

# --- 2. セッションを file に（DB 未接続時でも /top が動くように）---
echo "==> 2. セッションを file に変更（推奨）"
if grep -q '^SESSION_DRIVER=' "$API_ENV"; then
  sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' "$API_ENV"
else
  echo 'SESSION_DRIVER=file' >> "$API_ENV"
fi
echo "    SESSION_DRIVER=file にしました。"
echo ""

# --- 3. キャッシュ削除・hot 削除 ---
echo "==> 3. キャッシュ削除・hot 削除"
rm -f api/public/hot
$COMPOSE exec -T app php artisan config:clear
$COMPOSE exec -T app php artisan view:clear
echo "    config:clear / view:clear / rm hot 完了。"
echo ""

# --- 4. マイグレーション（sessions テーブル等）---
echo "==> 4. マイグレーション"
$COMPOSE exec -T app php artisan migrate --force 2>/dev/null || echo "    （マイグレーションでエラーが出た場合は DB 接続を確認してください）"
echo ""

# --- 5. DB 接続テスト ---
echo "==> 5. DB 接続テスト"
$COMPOSE exec -T app php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null && echo "    DB 接続 OK" || echo "    DB 接続失敗（ルート .env の DB_PASSWORD と api/.env を一致させてください）"
echo ""

# --- 6. 直近のエラーログ ---
echo "==> 6. 直近の Laravel ログ（参考）"
if [ -f api/storage/logs/laravel.log ]; then
  tail -30 api/storage/logs/laravel.log | sed 's/^/    /'
else
  echo "    （ログファイルなし）"
fi
echo ""
echo "=========================================="
echo "  ここまで完了。ブラウザで http://160.251.214.119/top を開いて確認してください。"
echo "  まだ 500 の場合は、上記ログの ERROR 行を確認するか、APP_DEBUG=true で例外内容を確認してください。"
echo "=========================================="
