# 本番デプロイ・最適手順（今までの話のまとめ）

**いつもこの順番でやればよい**という手順です。スクリプトは使わず、ここに書いたコマンドをコピペして実行してください。

---

## 重要ポイント（今までの失敗から）

1. **デプロイは必ずリポジトリのルートから**  
   `cd /Users/tsurumaruryoya/5ake-me` してから `./scripts/deploy-to-vps.sh`。`api` の中から実行しない。
2. **ビルドしてからデプロイ**  
   `api/public/build/` が無いと本番で 500 や localhost:5174 参照になる。
3. **api/.env の DB_PASSWORD とルート .env を同じにする**  
   違うと MySQL の laravel ユーザーで Access denied。
4. **本番では SESSION_DRIVER=file 推奨**  
   database のまま sessions テーブルや DB に問題があると 500 になりやすい。
5. **VPS に api/public/hot を残さない**  
   残っていると localhost:5174 を参照して CSP でブロックされる。

---

## A. 2回目以降の通常デプロイ（コードを更新したとき）

### Mac（リポジトリルートで実行）

```bash
cd /Users/tsurumaruryoya/5ake-me
cd api && npm run build
cd ..
./scripts/deploy-to-vps.sh
```

「再ビルドしますか?」→ **N** でよい（すでに上でビルドしているため）。

### VPS（必要ならだけ）

マイグレーションを追加したときだけ:

```bash
ssh ryoya@160.251.214.119
cd ~/5akeme
docker compose -f docker-compose.production.yml exec -T app php artisan migrate --force
exit
```

### 確認

ブラウザで **http://160.251.214.119/top** を開く。

---

## B. 500 が出たとき（まとめて直す）

### Mac（リポジトリルートで実行）

```bash
cd /Users/tsurumaruryoya/5ake-me
cd api && npm run build
cd ..
./scripts/deploy-to-vps.sh
```

### VPS（SSH してから、書いた順に 1 行ずつ）

```bash
ssh ryoya@160.251.214.119
```

入れたら:

```bash
cd ~/5akeme
```

```bash
nano api/.env
```

次を確認・修正する:

- `DB_USERNAME=laravel`
- `DB_HOST=db`
- `DB_PASSWORD=` の値を **ルートの `~/5akeme/.env` の DB_PASSWORD と完全に同じ**にする
- `SESSION_DRIVER=file` になっているか（`database` なら `file` に変更）

保存: **Ctrl+O** → Enter → **Ctrl+X**

```bash
rm -f api/public/hot
```

```bash
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
```

```bash
docker compose -f docker-compose.production.yml exec -T app php artisan view:clear
```

```bash
docker compose -f docker-compose.production.yml exec -T app php artisan migrate --force
```

### 確認

ブラウザで **http://160.251.214.119/top** を開く。まだ 500 なら:

```bash
docker compose -f docker-compose.production.yml exec app tail -50 storage/logs/laravel.log
```

で **ERROR** や **Exception** の行を確認する。

---

## クイック参照（コピペ用）

**Mac で毎回やること（デプロイするとき）:**

```bash
cd /Users/tsurumaruryoya/5ake-me
cd api && npm run build
cd ..
./scripts/deploy-to-vps.sh
```

**VPS で「おかしいな」と思ったときにやること:**

```bash
cd ~/5akeme
rm -f api/public/hot
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
docker compose -f docker-compose.production.yml exec -T app php artisan view:clear
```

---

## 関連ドキュメント

- 初回デプロイの詳細: [DEPLOY_CHECKLIST.md](./DEPLOY_CHECKLIST.md)
- 本番 .env の必須項目: [ENV_REQUIRED.md](./ENV_REQUIRED.md)
- 500 の詳細手順（コマンドのみ）: [PRODUCTION_500_FIX.md](./PRODUCTION_500_FIX.md)
