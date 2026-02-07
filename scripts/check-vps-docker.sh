#!/usr/bin/env bash
# VPS に Docker / Docker Compose が入っているかチェック
# 使い方: ./scripts/check-vps-docker.sh
# 事前: ssh ryoya@160.251.214.119 で鍵ログインできること

set -e
VPS="ryoya@160.251.214.119"

echo "VPS に接続して Docker / Compose を確認します..."
echo ""

ssh "$VPS" bash -s << 'REMOTE'
echo "=== 1. Docker の有無 ==="
if command -v docker &>/dev/null; then
  docker --version
else
  echo "Docker 未インストール"
  exit 1
fi

echo ""
echo "=== 2. Docker Compose プラグインの有無 ==="
if docker compose version &>/dev/null; then
  docker compose version
else
  echo "docker compose 未インストール（apt install docker-compose-plugin を検討）"
fi

echo ""
echo "=== 3. docker ps（権限確認） ==="
if docker ps 2>/dev/null; then
  echo "OK: ryoya で docker が実行可能"
else
  echo "NG: docker ps が失敗（usermod -aG docker ryoya のあと再ログインが必要かも）"
fi

echo ""
echo "=== 4. ユーザー・グループ ==="
whoami
id
echo ""
echo "チェック完了。"
REMOTE
