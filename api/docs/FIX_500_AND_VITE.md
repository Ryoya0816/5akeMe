# /top などが 500 で、コンソールに localhost:5174 が出るとき

## 原因

- 本番サーバに **Vite のビルド成果物（`api/public/build/`）が無い**と、Laravel の `@vite()` が失敗して 500 になることがあります。
- サーバに **`api/public/hot`** が残っていると、Vite が開発サーバー（localhost:5174）を参照し、CSP でブロックされます。

## 対処手順

### 1. Mac でビルドしてからデプロイ

```bash
cd /Users/tsurumaruryoya/5ake-me
cd api && npm ci && npm run build
cd ..
./scripts/deploy-to-vps.sh
```

（`deploy-to-vps.sh` は `api/public/build/manifest.json` が無いとビルドを促します。必ず **ビルドしてから** 送ること。）

### 2. VPS で hot を削除・キャッシュクリア

```bash
ssh ryoya@160.251.214.119
cd ~/5akeme
rm -f api/public/hot
docker compose -f docker-compose.production.yml exec -T app php artisan view:clear
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
```

### 3. 確認

- ブラウザで http://160.251.214.119/top を開く
- 開発者ツールのコンソールに localhost:5174 のエラーが出ず、ページが表示されれば OK

## 補足

- **DB の 500**（診断 start など）の対処は [DIAGNOSE_500_CHECK.md](./DIAGNOSE_500_CHECK.md) を参照。
- 本番では **`api/public/build/`** に `manifest.json` と JS/CSS が入っている必要があります。`api/public/hot` は開発用のため本番には置かないでください。
