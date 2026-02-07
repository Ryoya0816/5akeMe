# 5akeMe 本番デプロイ チェックリスト

## 初回デプロイ

### ローカル（Mac）

- [ ] `cd /path/to/5ake-me/api` → `npm ci && npm run build`
- [ ] `api/public/build/manifest.json` が存在する
- [ ] `api/.env` と ルート `.env` を用意（DB_PASSWORD を同じ値に）
- [ ] `./scripts/deploy-to-vps.sh` で rsync、または手動で rsync
- [ ] .env を送る: `./scripts/send-env-to-vps.sh` または scp

### VPS（ssh ryoya@160.251.214.119）

- [ ] `cd ~/5akeme` でコードがある
- [ ] `api/.env` と `.env` がある（送った or サーバで cp + nano）
- [ ] `docker compose -f docker-compose.production.yml build`
- [ ] `docker compose -f docker-compose.production.yml up -d`
- [ ] `docker compose -f docker-compose.production.yml exec app php artisan key:generate`
- [ ] `docker compose -f docker-compose.production.yml exec app php artisan config:cache`
- [ ] `docker compose -f docker-compose.production.yml exec app php artisan migrate --force`
- [ ] `docker compose -f docker-compose.production.yml exec app php artisan storage:link`
- [ ] `docker compose -f docker-compose.production.yml exec app php artisan route:cache`
- [ ] `docker compose -f docker-compose.production.yml exec app php artisan view:cache`

### 確認

- [ ] ブラウザで http://160.251.214.119 が開ける
- [ ] トップ・ログイン・診断が動作する

### お名前.com ドメインを張ったあと

- [ ] お名前.com で A レコードを 160.251.214.119 に設定
- [ ] api/.env の APP_URL と SESSION_DOMAIN をドメインに変更 → `php artisan config:cache`
- [ ] （任意）HTTPS: [DOMAIN_AND_SSL.md](./DOMAIN_AND_SSL.md) 参照

---

## 2 回目以降（更新デプロイ）

### ローカル

- [ ] コード変更 → `cd api && npm run build`
- [ ] `./scripts/deploy-to-vps.sh` または rsync

### VPS

- [ ] `cd ~/5akeme && docker compose -f docker-compose.production.yml exec app php artisan migrate --force`（必要なら）
- [ ] `docker compose -f docker-compose.production.yml exec app php artisan config:cache`（必要なら）

---

## リンク

- 詳細手順: [DEPLOY_NOW.md](./DEPLOY_NOW.md)
- VPS 初期設定: [SSH_CONOHA_VPS.md](./SSH_CONOHA_VPS.md)
- **ドメイン・SSL（お名前.com）:** [DOMAIN_AND_SSL.md](./DOMAIN_AND_SSL.md)
- **接続できないとき:** [CONNECTION_CHECK.md](./CONNECTION_CHECK.md)
- Git/本番分離: [GIT_AND_DEPLOY_CHECKLIST.md](./GIT_AND_DEPLOY_CHECKLIST.md)
