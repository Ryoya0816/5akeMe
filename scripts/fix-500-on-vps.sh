#!/usr/bin/env bash
# VPS 上で実行するスクリプト（readonly database 対策）
# 使い方: このファイルを VPS にコピーして bash fix-500-on-vps.sh
# または: ssh で入った状態で以下を手動で実行

set -e
cd ~/5akeme

echo "==> 1. セッション・キャッシュをファイルに変更（DB 書き込みを避ける）"
sed -i 's/SESSION_DRIVER=database/SESSION_DRIVER=file/' api/.env
sed -i 's/CACHE_STORE=database/CACHE_STORE=file/' api/.env

echo "==> 2. ストレージ権限"
docker compose -f docker-compose.production.yml exec -T app sh -c "chmod -R 775 storage bootstrap/cache 2>/dev/null; chown -R www-data:www-data storage bootstrap/cache 2>/dev/null; true"

echo "==> 3. 設定クリア＆キャッシュ"
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
docker compose -f docker-compose.production.yml exec -T app php artisan config:cache
docker compose -f docker-compose.production.yml exec -T app php artisan cache:clear

echo "==> 4. 確認"
grep -E 'SESSION_DRIVER|CACHE_STORE' api/.env
echo "Done. ブラウザで http://160.251.214.119/top を開き直してください。"
