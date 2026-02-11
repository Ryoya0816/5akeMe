# 本番で 500 が出たとき（コマンドで直す手順）

スクリプトは使わず、**ここに書いてあるコマンドを順に実行**してください。

---

## 前提

- 本番 URL: **http://160.251.214.119**
- VPS のプロジェクト: **~/5akeme**
- Mac のプロジェクト: **/Users/tsurumaruryoya/5ake-me**（自分のパスに読み替えてください）

---

## Step 1: Mac でビルドとデプロイ

**Mac のターミナルで:**

```bash
cd /Users/tsurumaruryoya/5ake-me
cd api && npm run build
cd ..
./scripts/deploy-to-vps.sh
```

「再ビルドしますか?」→ ビルド済みなら **N** でよい。

---

## Step 2: VPS に SSH する

**Mac のターミナルで:**

```bash
ssh ryoya@160.251.214.119
```

パスワード（または鍵）で入る。プロンプトが `ryoya@vm-xxx:~/5akeme$` のようなら OK。

---

## Step 3: VPS でやること（ここに書いた順に実行）

**必ず VPS にログインした状態で**、次のコマンドを **1 行ずつ** 実行してください。

### 3-1. プロジェクトディレクトリへ

```bash
cd ~/5akeme
```

### 3-2. api/.env の DB 設定を直す

- **DB_USERNAME=laravel** と **DB_HOST=db** にする。  
  すでに合っていればそのままでよい。
- **DB_PASSWORD** を、**ルートの .env**（`~/5akeme/.env`）の **DB_PASSWORD** と **同じ値**にする。

編集する場合:

```bash
nano api/.env
```

次の行を確認・修正する:

- `DB_USERNAME=laravel`
- `DB_HOST=db`
- `DB_PASSWORD=` の右に、ルート `.env` の DB_PASSWORD をそのままコピーして貼る

保存: **Ctrl+O** → Enter → **Ctrl+X**

### 3-3. セッションを file にする（500 になりにくくする）

同じく `nano api/.env` で、次の行を探して:

- `SESSION_DRIVER=database` → **`SESSION_DRIVER=file`** に変更  
  または、無ければ 1 行追加: `SESSION_DRIVER=file`

保存して終了。

### 3-4. 開発用の hot を消す

```bash
rm -f api/public/hot
```

### 3-5. Laravel のキャッシュを消す

```bash
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
docker compose -f docker-compose.production.yml exec -T app php artisan view:clear
```

### 3-6. マイグレーション（sessions テーブルなどを作る）

```bash
docker compose -f docker-compose.production.yml exec -T app php artisan migrate --force
```

エラーが出たら、3-2 の DB_PASSWORD がルート .env と一致しているか確認する。

### 3-7. DB 接続の確認（任意）

```bash
docker compose -f docker-compose.production.yml exec -T app php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';"
```

「OK」が出れば DB 接続は成功。

### 3-8. まだ 500 のときはログを確認

```bash
docker compose -f docker-compose.production.yml exec app tail -50 storage/logs/laravel.log
```

**production.ERROR** や **Exception** の行を見て、メッセージをメモする。その内容で原因を特定できる。

---

## Step 4: ブラウザで確認

**http://160.251.214.119/top** を開く。

- 表示されれば OK。
- まだ 500 → Step 3-8 のログの **ERROR / Exception** の内容を共有してもらえれば、次の対処を案内する。

---

## なぜ 500 になるか（整理）

| 原因 | やること |
|------|----------|
| DB の laravel ユーザー／パスワードが違う | api/.env の DB_USERNAME=laravel、DB_PASSWORD=ルート .env と同じにする（Step 3-2） |
| SESSION_DRIVER=database で DB や sessions テーブルに問題がある | SESSION_DRIVER=file にする（Step 3-3） |
| config のキャッシュが古い | config:clear（Step 3-5） |
| public/hot が残って localhost:5174 を参照している | rm -f api/public/hot（Step 3-4） |
| api/public/build/ がサーバに無い | Mac で npm run build してから deploy（Step 1） |

---

## 他のドキュメント

- **本番 .env の必須項目:** [ENV_REQUIRED.md](./ENV_REQUIRED.md)
- **500 と localhost:5174（Vite）:** [FIX_500_AND_VITE.md](./FIX_500_AND_VITE.md)
- **診断 500:** [DIAGNOSE_500_CHECK.md](./DIAGNOSE_500_CHECK.md)
