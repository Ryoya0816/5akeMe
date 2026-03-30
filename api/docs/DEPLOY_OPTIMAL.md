# 本番デプロイ・最適手順（今までの話のまとめ）

**いつもこの順番でやればよい**という手順です。

---

## 重要ポイント（今までの失敗から）

1. **デプロイは必ずリポジトリのルートから**  
   `cd /Users/tsurumaruryoya/5ake-me` してからスクリプトを実行。`api` の中から実行しない。
2. **ビルドしてからデプロイ**  
   `api/public/build/` が無いと本番で 500 や localhost:5174 参照になる。
3. **api/.env の DB_PASSWORD とルート .env を同じにする**  
   違うと MySQL の laravel ユーザーで Access denied。
4. **本番では SESSION_DRIVER=file 推奨**  
   database のまま sessions テーブルや DB に問題があると 500 になりやすい。
5. **VPS に api/public/hot を残さない**  
   残っていると localhost:5174 を参照して CSP でブロックされる。

---

## ★ 推奨: DB を含めてデプロイ（Docker の内容をそのまま本番へ）

**ローカル Docker で作った店舗・診断結果などを、そのまま本番に反映したいとき**はこちらを使います。

### 前提

- ローカルで `docker compose up -d` しておく（DB をダンプするため）
- ローカルで `cd api && composer install` 済み（vendor が必要）
- VPS の `~/5akeme/.env` に `DB_PASSWORD` が設定されていること
- VPS の `~/5akeme/api/.env` が用意されていること（初回は `fix-db-env-on-vps.sh` 参照）

### 手順（1 コマンド）

```bash
cd /Users/tsurumaruryoya/5ake-me
./scripts/deploy-with-db.sh
```

非対話モード（CI 等）: `./scripts/deploy-with-db.sh -y`

このスクリプトは以下を自動で行います:

1. 事前チェック（DB 起動、vendor 存在）
2. フロントビルド（必要なら）
3. コードを rsync で VPS へ転送（api/public/hot は送らない）
4. ローカル DB をダンプ
5. ダンプを VPS へ転送
6. **本番 DB をローカル DB で上書き**（インポート・MySQL 起動待機あり）
7. コンテナ再起動・storage:link・キャッシュクリア

### 注意

- **本番 DB は完全に上書きされます**。本番にしかないデータは消えます。
- ローカル Docker の DB が「正」になります。店舗追加・診断結果などはローカルで行い、このスクリプトで本番へ反映してください。

---

## A. コードのみデプロイ（DB は触らない）

店舗データや診断結果を変えず、**コードの変更だけ**本番に反映したいときは `deploy-to-vps.sh` を使います。

### A-0. ローカルで本番と同じ構成で確認（推奨）

```bash
cd /Users/tsurumaruryoya/5ake-me
cd api && npm run build
cd ..
docker compose -f docker-compose.staging.yml up -d --build
```

ブラウザで **http://localhost:8080** を開いて確認。  
（本番と同じ nginx + php-fpm + MySQL + Python の構成で動く。Vite dev server なし。）

- OK → A-1 に進んでデプロイ
- NG → コードを直して `npm run build` → もう一度 staging で確認

止めるとき:
```bash
docker compose -f docker-compose.staging.yml down
```

### A-1. Mac からデプロイ（リポジトリルートで実行）

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

## 本番（VPS）をローカルに移す

デプロイ済みの本番データを **localhost の Docker でそのまま使いたい**ときの手順。

### 1. VPS で DB をダンプ

VPS に SSH して実行（パスワードは本番の DB_PASSWORD）:

```bash
ssh ryoya@160.251.214.119
cd ~/5akeme
docker compose -f docker-compose.production.yml exec -T db mysqldump -u laravel -p --no-tablespaces laravel > ~/db_dump.sql
```

入力後 `exit` で抜ける。

### 2. Mac にダウンロード

```bash
cd /Users/tsurumaruryoya/5ake-me
scp ryoya@160.251.214.119:~/db_dump.sql ./db_dump.sql
```

### 3. ローカル Docker を起動

```bash
docker compose up -d
```

### 4. ローカル MySQL にインポート

```bash
docker compose exec -T db mysql -u laravel -plaravel laravel < ./db_dump.sql
```

（ローカルは `docker-compose.yml` でパスワードが `laravel`）

### 5. ダンプ削除（任意）

```bash
rm -f db_dump.sql
```

### 6. 確認

ブラウザで **http://localhost:8082** を開く。本番と同じデータで動いていれば OK。

**補足:** コードはすでにローカルにある想定。**ローカルで Docker を使うとき**は `api/.env` を次のようにする（本番と別）:
- `APP_ENV=local`（または `development`）→ CSP で localhost:5174 を許可
- `APP_DEBUG=true` → エラー内容を表示
- `APP_URL=http://localhost:8082`
- `DB_PASSWORD=laravel`（ローカル Docker の MySQL は `docker-compose.yml` で laravel）
- `APP_KEY` が空ならコンテナ内で `php artisan key:generate`

---

## 店舗・診断結果のデータが消えたら

**チェック結果:** シーダー（StoreSeeder 等）に「全削除」する処理はありません。データが消える主な原因は次のどれかです。

1. **`php artisan migrate:fresh` または `migrate:fresh --seed` を実行した**  
   → 全テーブルを drop して作り直すため、店舗・診断結果も消えます。
2. **Docker のボリュームを消した**（例: `docker compose down -v` や `docker volume rm ... dbdata`）  
   → MySQL のデータが丸ごと消えます。

**復元するには:** 上記「本番（VPS）をローカルに移す」の手順で、VPS から DB をダンプし直し、ローカルにインポートし直してください。本番に店舗・診断結果が残っていれば、それで復元できます。

**今後の運用（推奨）:**  
- 店舗追加・診断結果などは **ローカル Docker で行う**  
- デプロイ時は **`./scripts/deploy-with-db.sh`** で DB ごと本番へ反映  
- ローカルで **`migrate:fresh` は使わない**（必要なマイグレーションだけ `migrate` で実行）  
- **`docker compose down` するときは `-v` を付けない**（`-v` はボリューム削除）

---

## クイック参照（コピペ用）

**Mac で毎回やること（DB も含めてデプロイ）:**

```bash
cd /Users/tsurumaruryoya/5ake-me
docker compose up -d   # DB が起動していること
./scripts/deploy-with-db.sh
```

**コードだけデプロイ（DB は触らない）:**

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

## デプロイ時のよくあるエラーと対処

| エラー | 原因 | 対処 |
|--------|------|------|
| `ローカル DB が起動していません` | Docker の db コンテナが止まっている | `docker compose up -d` を実行 |
| `api/vendor がありません` | composer install 未実行 | `cd api && composer install` |
| `ダンプが空です` | MySQL 接続失敗 | DB コンテナの状態確認、`docker compose logs db` |
| `DB_PASSWORD が設定されていません` | VPS の `~/5akeme/.env` に DB_PASSWORD がない | nano で .env を編集して追加 |
| `DB インポートに失敗しました` | MySQL がまだ起動中、またはパスワード不一致 | 数分待って再実行、または api/.env の DB_PASSWORD をルート .env と一致させる |
| 本番で 500 エラー | manifest.json なし、hot 残存、DB 接続失敗、APP_KEY なし | 上記 **「B. 500 が出たとき」** を実行 |
| `Access denied for user 'laravel'` | api/.env の DB_PASSWORD がルート .env と違う | `fix-db-env-on-vps.sh` を実行 |

---

## 関連ドキュメント

- 初回デプロイの詳細: [DEPLOY_CHECKLIST.md](./DEPLOY_CHECKLIST.md)
- 本番 .env の必須項目: [ENV_REQUIRED.md](./ENV_REQUIRED.md)
- 500 の手順: このファイルの **「B. 500 が出たとき（まとめて直す）」**。診断 API は [ENV_REQUIRED.md](./ENV_REQUIRED.md) の DB 設定も確認
