#!/usr/bin/env bash
# サーバの api/.env から不正な行（変数名が「あお」など）を削除する
# 使い方: ./scripts/fix-env-on-vps.sh
# 事前: ssh ryoya@160.251.214.119 で鍵ログインできること

set -e
VPS="ryoya@160.251.214.119"

echo "VPS の api/.env をバックアップし、不正な行を削除します..."
ssh "$VPS" 'cd ~/5akeme/api && cp .env .env.bak && sed -i "/^[[:space:]]*あお/d" .env 2>/dev/null; sed -i "/^あお/d" .env 2>/dev/null; echo "Done. Backup: api/.env.bak"'
echo ""
echo "続けて VPS で実行: docker compose -f docker-compose.production.yml exec app composer dump-autoload && docker compose -f docker-compose.production.yml exec app php artisan config:clear"
