# プロジェクトルートの Makefile（docker-compose.yml があるディレクトリで実行）
COMPOSE = docker compose

# ---------------------------------------------------------------------------
# フロントエンド（Vite / CSS/JS）— すべて Docker 内で実行
# ---------------------------------------------------------------------------

# npm ci + npm run build を node コンテナで実行（ビルド成果物は api/public/build/ に出力）
assets:
	$(COMPOSE) run --rm node sh -c "npm ci && npm run build"

# ビルドのみ（node_modules がすでにあるとき。assets より速い）
assets-build:
	$(COMPOSE) run --rm node npm run build

# Vite 開発サーバーを起動（HMR 有効。node コンテナで npm run dev）
assets-dev:
	$(COMPOSE) up node

# npm や npx を Docker 内で実行するときの例: make npm ARGS="run build"
npm:
	$(COMPOSE) run --rm node npm $(ARGS)

.PHONY: assets assets-build assets-dev npm
