# Git に上げてはいけないもの / 本番と検証の分離

## 1. Git にコミットしてはいけないもの

以下は **絶対にコミットしない** こと。`.gitignore` で除外済みだが、新規ファイル追加時は確認すること。

| 種類 | 例 | 理由 |
|------|-----|------|
| **環境変数（秘密含む）** | `.env`, `.env.production`, `.env.local`, ルートの `.env` | APP_KEY, DB_PASSWORD, OAuth の Client Secret 等が含まれる |
| **Laravel ストレージ** | `api/storage/logs/*`, `api/storage/framework/views/*`, `api/storage/framework/cache/*`, `api/storage/framework/sessions/*` | 実行時生成・ログ・セッション |
| **Laravel キー** | `api/storage/*.key` | 暗号化キー |
| **ビルド成果物** | `api/public/build/`, `api/public/hot` | Vite ビルド結果（ローカルで build してからデプロイ） |
| **依存** | `api/vendor/`, `api/node_modules/` | composer / npm で再インストールする |
| **Python** | `python/app/__pycache__/`, `python/app/data/` | キャッシュ・学習データ（本番はボリュームで保持） |
| **ローカル上書き** | `docker-compose.override.yml` | 環境ごとの上書き（コミットしない） |

**コミットしてよいもの**

- `.env.example`（api/.env.example, ルート .env.example）… プレースホルダーのみ。実際の値は書かない。
- ソースコード・設定テンプレート・ドキュメント・マイグレーション

---

## 2. 本番デプロイ用と検証・開発用の分離

| 用途 | 使うファイル・設定 | 秘密の扱い |
|------|---------------------|------------|
| **本番** | `docker-compose.production.yml` | サーバ上で `api/.env` とルート `.env` を手動で用意（rsync で `.env` を送らない） |
| **検証・開発** | `docker-compose.yml` | ローカルで `api/.env` を `.env.example` からコピーして編集。本番のパスワードは使わない |
| **Laravel Sail** | `api/compose.yaml` | `api/.env` の `${DB_PASSWORD}` 等を参照。開発用 |

- **本番**: パスワード・APP_KEY はサーバで `php artisan key:generate` や手動設定。リポジトリには一切含めない。
- **検証**: `api/.env.example` を `api/.env` にコピーし、ローカル用の値（例: DB_PASSWORD=laravel）のみ設定。本番と同じ値にしないこと。

---

## 3. デプロイ前の最終確認

- [ ] `git status` で `.env` や `.env.production` が含まれていないこと
- [ ] `docker-compose.yml` は開発用であること（本番では `docker-compose.production.yml` を使う）
- [ ] 本番サーバには `api/.env` と（compose 用）ルート `.env` をサーバ上で作成し、本番用の値だけを設定していること
- [ ] rsync / デプロイスクリプトで `.env` を上書きしていないこと（意図して送る場合は別）
