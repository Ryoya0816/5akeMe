#!/usr/bin/env bash
# 本番サイトに接続できるかチェック（Mac から実行）
# 使い方: ./scripts/check-vps-connection.sh

set -e
VPS_IP="${VPS_IP:-160.251.214.119}"
VPS_USER="ryoya"

echo "=== 1. Ping（VPS が生きているか） ==="
if ping -c 2 -W 3 "$VPS_IP" &>/dev/null; then
  echo "OK: $VPS_IP に応答あり"
else
  echo "NG: $VPS_IP に ping が通らない（VPS 停止 or ファイアウォール）"
fi

echo ""
echo "=== 2. ポート 80（HTTP）==="
if curl -s -o /dev/null -w "" --connect-timeout 5 "http://$VPS_IP/" 2>/dev/null; then
  echo "OK: ポート 80 に接続できた"
else
  echo "NG: ポート 80 に接続できない（ConoHa ファイアウォールで 80 許可 or nginx 起動確認）"
fi

echo ""
echo "=== 3. HTTP 取得（curl） ==="
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 "http://$VPS_IP/" 2>/dev/null || echo "000")
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
  echo "OK: http://$VPS_IP/ → HTTP $HTTP_CODE"
elif [ "$HTTP_CODE" = "000" ]; then
  echo "NG: 接続できません（タイムアウト or 拒否）"
else
  echo "HTTP $HTTP_CODE （200/302 なら OK）"
fi

echo ""
echo "=== 4. SSH（鍵ログイン） ==="
if ssh -o ConnectTimeout=5 -o BatchMode=yes "$VPS_USER@$VPS_IP" "echo OK" 2>/dev/null; then
  echo "OK: SSH でログイン可能"
  echo ""
  echo "VPS 上でコンテナ確認するには:"
  echo "  ssh $VPS_USER@$VPS_IP 'cd ~/5akeme && docker compose -f docker-compose.production.yml ps'"
else
  echo "NG または スキップ: SSH できません（鍵未設定の場合は手動で ssh $VPS_USER@$VPS_IP を試す）"
fi

echo ""
echo "=== チェック完了 ==="
