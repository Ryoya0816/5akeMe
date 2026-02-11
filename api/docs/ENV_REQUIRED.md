# 本番サーバ .env 必須項目（VPS）

VPS 上では **2 つの .env** を使います。**DB のユーザー名とパスワードは必ず揃えてください。**

## 1. ルート `.env`（`~/5akeme/.env`）

Docker Compose と MySQL 用。

| 変数 | 必須 | 説明 |
|------|------|------|
| `DB_PASSWORD` | ✅ | MySQL の root と laravel ユーザーのパスワード（強めの文字列） |
| `DB_DATABASE` | 推奨 | `laravel`（省略時も laravel） |
| `DB_USERNAME` | 推奨 | `laravel`（Compose で MYSQL_USER に使う） |

## 2. Laravel 用 `api/.env`（`~/5akeme/api/.env`）

| 変数 | 必須 | 説明 |
|------|------|------|
| `DB_CONNECTION` | ✅ | `mysql` |
| `DB_HOST` | ✅ | **`db`**（コンテナ名。localhost ではない） |
| `DB_PORT` | ✅ | `3306` |
| `DB_DATABASE` | ✅ | `laravel` |
| **`DB_USERNAME`** | ✅ | **`laravel`**（root にしない） |
| **`DB_PASSWORD`** | ✅ | **ルート .env の `DB_PASSWORD` と同一の値** |
| `SESSION_DRIVER` | 推奨 | `file`（DB 未接続時は file が安全） |
| `APP_KEY` | ✅ | `php artisan key:generate` で生成 |
| `APP_URL` | ✅ | 本番 URL（例: `http://160.251.214.119`） |

## よくあるエラー

- **Access denied for user 'root'@'...' (using password: NO)**  
  → `api/.env` の `DB_USERNAME=laravel` と `DB_PASSWORD=（ルート .env と同じ値）` を設定し、`php artisan config:clear` してから再試行。

## 修正スクリプト（VPS 上で実行）

リポジトリの `scripts/fix-db-env-on-vps.sh` を VPS に送って実行すると、`api/.env` の `DB_USERNAME` と `DB_HOST` を自動で直せます。`DB_PASSWORD` はルート .env と違う場合のみ手動で合わせてください。

```bash
# Mac から
scp scripts/fix-db-env-on-vps.sh ryoya@160.251.214.119:~/5akeme/
ssh ryoya@160.251.214.119 'cd ~/5akeme && bash fix-db-env-on-vps.sh'
```

## 接続確認（VPS 上）

```bash
cd ~/5akeme
docker compose -f docker-compose.production.yml exec app php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';"
```

「OK」が出れば DB 接続成功です。
