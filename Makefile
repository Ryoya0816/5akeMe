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

# ---------------------------------------------------------------------------
# データベースドキュメント生成（tbls）
# ---------------------------------------------------------------------------

# tblsバイナリのパス（プロジェクト内のbin/tblsを使用）
TBLS = ./bin/tbls

# tblsでテーブル定義書とER図を生成
docs:
	@echo "Generating database documentation..."
	@mkdir -p docs/schema
	$(TBLS) doc --config tbls.yml

# tblsでテーブル定義書のみ生成（ER図も含まれます）
docs-doc:
	@echo "Generating table documentation..."
	@mkdir -p docs/schema
	$(TBLS) doc --config tbls.yml

# ---------------------------------------------------------------------------
# 本番デプロイ用コマンド
# ---------------------------------------------------------------------------

# 本番用ビルド（アセット + Composer最適化）
deploy-build:
	@echo "Building for production..."
	$(COMPOSE) run --rm node sh -c "npm ci && npm run build"
	$(COMPOSE) exec app composer install --optimize-autoloader --no-dev
	$(COMPOSE) exec app php artisan config:cache
	$(COMPOSE) exec app php artisan route:cache
	$(COMPOSE) exec app php artisan view:cache
	@echo "Production build complete!"

# キャッシュクリア
deploy-clear:
	@echo "Clearing caches..."
	$(COMPOSE) exec app php artisan cache:clear
	$(COMPOSE) exec app php artisan config:clear
	$(COMPOSE) exec app php artisan route:clear
	$(COMPOSE) exec app php artisan view:clear
	@echo "Caches cleared!"

# マイグレーション実行
deploy-migrate:
	@echo "Running migrations..."
	$(COMPOSE) exec app php artisan migrate --force
	@echo "Migrations complete!"

# 本番デプロイ（フル）
deploy:
	@echo "Starting full deployment..."
	$(MAKE) deploy-clear
	$(MAKE) deploy-build
	$(MAKE) deploy-migrate
	@echo "Deployment complete!"

# メンテナンスモード ON
maintenance-on:
	$(COMPOSE) exec app php artisan down

# メンテナンスモード OFF
maintenance-off:
	$(COMPOSE) exec app php artisan up

# ヘルスチェック
health:
	@curl -s http://localhost:8082/up && echo " OK" || echo " FAILED"

.PHONY: assets assets-build assets-dev npm docs docs-doc deploy-build deploy-clear deploy-migrate deploy maintenance-on maintenance-off health
