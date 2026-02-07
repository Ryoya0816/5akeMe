# 今すぐデプロイ（ConoHa VPS）

VPS で **Docker / Compose が導入済み** である前提。未実施なら [SSH_CONOHA_VPS.md](./SSH_CONOHA_VPS.md) の「2) Docker / Compose 導入」を先に実行する。

---

## Step A: ローカル（Mac）でやること

### 1. ビルド

```bash
cd /path/to/5ake-me/api
npm ci
npm run build
```

※ 本リポジトリで `terser` 未導入の場合は `npm install -D terser` を先に実行。

### 2. サーバへコードを送る

```bash
cd /path/to/5ake-me
rsync -avz \
  --exclude='.git' --exclude='node_modules' --exclude='.env' --exclude='.env.*' \
  --exclude='api/storage/framework/views/' --exclude='api/storage/framework/sessions/' \
  --exclude='api/storage/framework/cache/' --exclude='api/storage/logs/' \
  ./ ryoya@160.251.214.119:~/5akeme/
```

**または** スクリプトでビルド＋rsync まで一括:

```bash
cd /path/to/5ake-me
./scripts/deploy-to-vps.sh
```

### ローカルで .env を作ってある場合（送るだけ）

ルートに `api/.env` と `.env` を用意済みなら、rsync のあとに scp で送る:

```bash
cd /path/to/5ake-me
scp api/.env ryoya@160.251.214.119:~/5akeme/api/
scp .env ryoya@160.251.214.119:~/5akeme/
```

※ `DB_PASSWORD` は api/.env と .env で同じ値にすること。

---

## Step B: VPS 上でやること

SSH で入る:

```bash
ssh ryoya@160.251.214.119
```

### 初回だけ: .env を用意

```bash
cd ~/5akeme
cp api/.env.example api/.env
cp .env.example .env
nano api/.env
```

**api/.env で必ず設定:**

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=http://160.251.214.119`
- `DB_DATABASE=laravel`
- `DB_USERNAME=laravel`
- `DB_PASSWORD=強力なパスワード`
- `PYTHON_API_URL=http://python:8000`

**ルート .env で:**

- `DB_DATABASE=laravel`
- `DB_USERNAME=laravel`
- `DB_PASSWORD=api/.env と同じパスワード`

### Compose で起動

```bash
cd ~/5akeme
docker compose -f docker-compose.production.yml build
docker compose -f docker-compose.production.yml up -d
```

### Laravel の初期設定（初回のみ）

```bash
docker compose -f docker-compose.production.yml exec app php artisan key:generate
docker compose -f docker-compose.production.yml exec app php artisan config:cache
docker compose -f docker-compose.production.yml exec app php artisan migrate --force
docker compose -f docker-compose.production.yml exec app php artisan storage:link
docker compose -f docker-compose.production.yml exec app php artisan route:cache
docker compose -f docker-compose.production.yml exec app php artisan view:cache
```

---

## 確認

ブラウザで **http://160.251.214.119** を開く。トップが表示されれば OK。

---

## 2 回目以降のデプロイ

1. **ローカル:** コードを直す → `cd api && npm run build` → `rsync`（または `./scripts/deploy-to-vps.sh`）
2. **VPS:** `cd ~/5akeme && docker compose -f docker-compose.production.yml exec app php artisan migrate --force`（必要なら）とキャッシュ系コマンド

詳細は [DEPLOY_CONOHA_5AKEME.md](./DEPLOY_CONOHA_5AKEME.md) を参照。
