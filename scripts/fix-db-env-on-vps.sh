#!/usr/bin/env bash
# VPS 上で実行: api/.env の DB 設定を直す（root/パスワードなし → laravel + ルート .env のパスワード）
# 使い方: このファイルを VPS にコピーして実行
#   scp scripts/fix-db-env-on-vps.sh ryoya@160.251.214.119:~/5akeme/
#   ssh ryoya@160.251.214.119 'cd ~/5akeme && bash fix-db-env-on-vps.sh'
# または VPS に SSH した状態で:
#   cd ~/5akeme && bash fix-db-env-on-vps.sh  （ファイルを手動で作るか scp で送ったあと）

set -e
cd ~/5akeme || { echo "~/5akeme がありません"; exit 1; }

API_ENV="api/.env"
ROOT_ENV=".env"

echo "==> 1. api/.env の DB 設定を確認・修正"

# DB_USERNAME を laravel に
if grep -q '^DB_USERNAME=' "$API_ENV" 2>/dev/null; then
  sed -i 's/^DB_USERNAME=.*/DB_USERNAME=laravel/' "$API_ENV"
else
  echo 'DB_USERNAME=laravel' >> "$API_ENV"
fi

# DB_HOST を db に
if grep -q '^DB_HOST=' "$API_ENV" 2>/dev/null; then
  sed -i 's/^DB_HOST=.*/DB_HOST=db/' "$API_ENV"
else
  echo 'DB_HOST=db' >> "$API_ENV"
fi

# DB_PASSWORD: ルート .env から取得して api/.env に設定（特殊文字も安全に扱う）
if [ -f "$ROOT_ENV" ]; then
  ROOT_PASS=$(grep '^DB_PASSWORD=' "$ROOT_ENV" 2>/dev/null | cut -d= -f2-)
  if [ -n "$ROOT_PASS" ]; then
    export ROOT_PASS
    if grep -q '^DB_PASSWORD=' "$API_ENV" 2>/dev/null; then
      awk 'BEGIN{pass=ENVIRON["ROOT_PASS"]} /^DB_PASSWORD=/{print "DB_PASSWORD=" pass; next} {print}' "$API_ENV" > "$API_ENV.tmp" && mv "$API_ENV.tmp" "$API_ENV"
    else
      awk 'BEGIN{pass=ENVIRON["ROOT_PASS"]} {print} END{print "DB_PASSWORD=" pass}' "$API_ENV" > "$API_ENV.tmp" && mv "$API_ENV.tmp" "$API_ENV"
    fi
    echo "    DB_PASSWORD をルート .env の値で設定しました。"
  else
    echo "    警告: ルート .env に DB_PASSWORD がありません。nano .env で DB_PASSWORD=任意のパスワード を追加してから再実行してください。"
    exit 1
  fi
else
  echo "    警告: ルート .env がありません。~/5akeme/.env を作成し DB_PASSWORD= を設定してください。"
  exit 1
fi

echo ""
echo "==> 2. 現在の DB 設定（DB_PASSWORD は伏せて表示）"
grep -E '^DB_' "$API_ENV" 2>/dev/null | sed 's/^DB_PASSWORD=.*/DB_PASSWORD=***設定済み***/' || true
if ! grep -q '^DB_PASSWORD=.' "$API_ENV" 2>/dev/null; then
  echo "    エラー: api/.env に DB_PASSWORD がありません。上記で終了した場合はルート .env に DB_PASSWORD を追加して再実行してください。"
  exit 1
fi

echo ""
echo "==> 3. config:clear"
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear

echo ""
echo "==> 4. 接続テスト"
docker compose -f docker-compose.production.yml exec -T app php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null && echo "DB 接続 OK" || echo "DB 接続まだ失敗。api/.env の DB_PASSWORD をルート .env と一致させてください"
