# 5akeMe 本番デプロイ手順（ConoHa VPS / Docker Compose）

VPS の初期設定（SSH・Docker 導入・nginx 疎通）が済んでいる前提。未実施の場合は [SSH_CONOHA_VPS.md](./SSH_CONOHA_VPS.md) の「次にやること」1〜3 を先に実行する。

**Git に上げてはいけないもの・本番と検証の分離**は [GIT_AND_DEPLOY_CHECKLIST.md](./GIT_AND_DEPLOY_CHECKLIST.md) を参照。

---

## 前提

- VPS: 160.251.214.119（ryoya で SSH 鍵ログイン可能）
- Docker / Docker Compose 導入済み
- **Vite ビルドはローカルで行い、成果物（api/public/build）をサーバに配置する**（サーバでは node を動かさない）

---

## 1. ローカルでビルド

```bash
# リポジトリルートで
cd api
npm ci
npm run build
cd ..
```

`api/public/build/` 以下に成果物ができることを確認する。

---

## 2. サーバにコードを配置

```bash
# 例: rsync（.git を除く）
rsync -avz \
  --exclude='.git' --exclude='node_modules' --exclude='.env' --exclude='.env.*' \
  --exclude='api/storage/framework/views/' --exclude='api/storage/framework/sessions/' \
  --exclude='api/storage/framework/cache/' --exclude='api/storage/logs/' \
  ./ ryoya@160.251.214.119:~/5akeme/

# または git clone + pull でサーバ上で取得
# ssh ryoya@160.251.214.119
# git clone <repo> ~/5akeme && cd ~/5akeme && git pull
```

サーバ側で `.env` を用意する（次の節）。

---

## 3. サーバ上で .env を用意

```bash
ssh ryoya@160.251.214.119
cd ~/5akeme
cp api/.env.example api/.env
nano api/.env
```

**本番で必ず設定する項目:**

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=` → `php artisan key:generate` で生成（コンテナ内で実行する場合は 4 のあと）
- `APP_URL=http://160.251.214.119`（ドメインを張る場合はその URL）
- `DB_DATABASE=laravel`（または任意のDB名）
- `DB_USERNAME=laravel`
- `DB_PASSWORD=`（強力なパスワード）
- `PYTHON_API_URL=http://python:8000`（compose 内なのでこのままでよい）

ルートに Docker Compose 用の `.env` も置く場合（DB のパスワードを compose に渡す）:

```bash
# ~/5akeme/.env（ルート）
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=ここにapi/.envと同じパスワード
```

---

## 4. 本番 Compose で起動

```bash
cd ~/5akeme
docker compose -f docker-compose.production.yml build
docker compose -f docker-compose.production.yml up -d
```

APP_KEY が未生成なら:

```bash
docker compose -f docker-compose.production.yml exec app php artisan key:generate
docker compose -f docker-compose.production.yml exec app php artisan config:cache
```

マイグレーション:

```bash
docker compose -f docker-compose.production.yml exec app php artisan migrate --force
```

ストレージリンク:

```bash
docker compose -f docker-compose.production.yml exec app php artisan storage:link
```

キャッシュ:

```bash
docker compose -f docker-compose.production.yml exec app php artisan config:cache
docker compose -f docker-compose.production.yml exec app php artisan route:cache
docker compose -f docker-compose.production.yml exec app php artisan view:cache
```

---

## 5. 動作確認

- ブラウザで **http://160.251.214.119** を開く
- トップ・ログイン・診断フローが表示されれば OK

---

## 6. 更新手順

```bash
# ローカルで
cd api && npm run build && cd ..

# サーバにコードを送る
rsync -avz \
  --exclude='.git' --exclude='node_modules' --exclude='.env' --exclude='.env.*' \
  --exclude='api/storage/framework/views/' --exclude='api/storage/framework/sessions/' \
  --exclude='api/storage/framework/cache/' --exclude='api/storage/logs/' \
  ./ ryoya@160.251.214.119:~/5akeme/

# サーバで
ssh ryoya@160.251.214.119
cd ~/5akeme
docker compose -f docker-compose.production.yml exec app php artisan migrate --force
docker compose -f docker-compose.production.yml exec app php artisan config:cache
docker compose -f docker-compose.production.yml exec app php artisan route:cache
docker compose -f docker-compose.production.yml exec app php artisan view:cache
# 必要ならコンテナの再ビルド
# docker compose -f docker-compose.production.yml up -d --build
```

---

## トラブルシューティング

- **502 Bad Gateway**  
  - `app` コンテナが起動しているか: `docker compose -f docker-compose.production.yml ps`  
  - Laravel のログ: `docker compose -f docker-compose.production.yml exec app tail -f storage/logs/laravel.log`

- **DB 接続エラー**  
  - `api/.env` の `DB_*` が、compose の `db` サービス（MYSQL_USER / MYSQL_PASSWORD / MYSQL_DATABASE）と一致しているか確認。

- **診断スコアが返らない**  
  - Python API の疎通: `docker compose -f docker-compose.production.yml exec app curl -s http://python:8000/health`  
  - `PYTHON_API_URL=http://python:8000` になっているか確認。

- **1GB でメモリ不足**  
  - MySQL は 400MB 制限済み。さらに厳しい場合は `docker-compose.production.yml` の `db.deploy.resources.limits.memory` を下げるか、SQLite / 外部 DB を検討。
