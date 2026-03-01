# 5akeMe（サケミー）

診断から「お酒の好み」を推定し、レコメンドにつなげる Web アプリのプロトタイプです。

---

## クイックスタート

```bash
docker compose up -d
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan migrate
```

ブラウザで **http://localhost:8082** を開いてください。

- ログインするにはテストユーザーを作成: `docker compose exec app php artisan user:create-hello`
- 詳細なセットアップ・ログイン方法は [api/README.md](api/README.md) を参照

---

## リポジトリ構成

| ディレクトリ | 内容 |
|-------------|------|
| **api/** | Laravel アプリ（診断・認証・店舗・管理画面） |
| **docs/** | 設計・運用ドキュメント（診断精度、SNSログイン、DB スキーマなど） |
| **docker/** | PHP / Nginx 等の Docker 設定 |
| **scripts/** | デプロイ用スクリプト |

---

## ドキュメント

- [api/README.md](api/README.md) — 開発・セットアップ（日本語）
- [docs/TECH_STACK.md](docs/TECH_STACK.md) — 技術スタック一覧
- [docs/DIAGNOSE_ACCURACY.md](docs/DIAGNOSE_ACCURACY.md) — 診断精度の改善方針
- [docs/SNS_LOGIN.md](docs/SNS_LOGIN.md) — SNS ログイン（Google / LINE / X）設定
- [docs/README.md](docs/README.md) — DB スキーマ・ER 図の生成方法
