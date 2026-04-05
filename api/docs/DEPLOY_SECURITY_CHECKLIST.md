# デプロイ前 セキュリティ・チェックリスト

デプロイ前に確認する項目です。本番環境では必ず実施してください。

---

## 1. 環境変数（.env）

| 項目 | 確認内容 | 本番推奨 |
|------|----------|----------|
| `APP_ENV` | 本番は `production` | `production` |
| `APP_DEBUG` | 必ず false | `false` |
| `APP_KEY` | 32文字のキーが設定されている | `php artisan key:generate` で生成済み |
| `APP_URL` | 本番ドメイン（https） | `https://your-domain.com` |
| `LOG_CHANNEL` | ログの出力先 | `stack` または `daily`（ファイルに残す） |
| `SESSION_SECURE_COOKIE` | HTTPS 時は true | `true`（HTTPS 運用時） |
| `SESSION_SAME_SITE` | そのままでOK | `lax`（既定） |
| `DB_*` | 本番DBの接続情報 | 本番用DB |
| `REDIS_PASSWORD` | Redis 使用時は設定 | 必要に応じて |
| `MAIL_*` | メール送信する場合は設定 | 本番用SMTP等 |
| ソーシャルログイン | Google/LINE等を使う場合 | `GOOGLE_CLIENT_*` 等を本番用に設定 |

**注意**: `.env` はリポジトリにコミットしないこと（`.gitignore` 済み）。本番サーバーで手動またはデプロイスクリプトで配置してください。

---

## 2. 認証・認可

| 項目 | 状態 |
|------|------|
| Web フォーム | CSRF トークンあり（`@csrf` / meta csrf-token） |
| API（診断スコア等） | バリデーションあり（answers のキー・型・長さ制限） |
| 管理画面（Filament `/admin`） | ログイン必須・VerifyCsrfToken あり |
| 年齢確認 | Cookie `age_verified` で制御（要・トップ等は `age.verified` ミドルウェア） |
| 停止ユーザー | `CheckUserActive` でログアウト・リダイレクト |

---

## 3. HTTP セキュリティヘッダー

`SecurityHeaders` ミドルウェアで以下を付与しています。

| ヘッダー | 内容 |
|----------|------|
| X-XSS-Protection | 1; mode=block |
| X-Frame-Options | SAMEORIGIN |
| X-Content-Type-Options | nosniff |
| Referrer-Policy | strict-origin-when-cross-origin |
| Permissions-Policy | camera/microphone/geolocation 無効 |
| Strict-Transport-Security | 本番のみ（HTTPS 強制） |
| Content-Security-Policy | `config/security.php` で定義。script/style/img/connect/form-action 等を制限 |

本番では `connect-src` に localhost を含めず、お問い合わせフォーム用に `form-action` に `https://formsubmit.co` を追加済みです。

---

## 4. 入力・出力

| 項目 | 状態 |
|------|------|
| Web フォーム向けサニタイズ | `SanitizeInput`（**web ミドルウェアのみ**）で NULL バイト・制御文字除去。API の JSON はミュテーションせずバリデーションで扱う |
| Blade 出力 | `{{ }}` でエスケープ（XSS 対策） |
| 診断 API | `answers` のキーを許可リストでフィルタ、値は string\|max:50 |
| 店舗サジェスト API | `mood`（integer in:0,1,2）、`primary`（string\|alpha_dash\|max:50） |
| Filament ウィジェット | `selectRaw('primary_type, COUNT(*) as count')` 等はカラム名固定（SQL インジェクションなし） |

---

## 5. レート制限

| 対象 | 制限 |
|------|------|
| API 全体 | 60 リクエスト/分（`throttleApi`） |
| 診断 API | 30 リクエスト/分（`throttle:30,1`） |
| 店舗レポート | 5 リクエスト/分（`throttle:5,1`） |
| ヘルスチェック | 10 リクエスト/分（`throttle:10,1`） |

---

## 6. ルート・運用

| 項目 | 確認内容 |
|------|----------|
| `/up` | Laravel 標準のヘルスチェック（詳細出さない） |
| `/health/db`, `/health/redis` | DB/Redis の疎通のみ（throttle あり）。必要なら本番では IP 制限や Basic 認証を検討 |
| `/debug/queue` | 非本番のみで定義（`app()->isProduction()` でガード） |
| 管理画面 | `/admin` は Filament のログイン必須。管理用ユーザーのみに共有すること |

---

## 7. その他

| 項目 | 推奨 |
|------|------|
| パスワード | Laravel 標準の bcrypt（Breeze 等でハッシュ化） |
| 秘密情報 | すべて `.env` 経由。コード内にハードコードしない |
| ログ | 本番ではスタックトレースを公開しない（APP_DEBUG=false で十分）。ログファイルの権限・保管期間を決めておく |
| お問い合わせフォーム | Formsubmit.co に送信。送信先メールは `contact.blade.php` の `action` の URL で指定（必要なら本番用アドレスに変更） |

---

## 8. デプロイ直後の確認

- [ ] `APP_DEBUG=false` でエラーがスタックトレース表示されないこと
- [ ] `https://` でアクセスし、HSTS とセッション Cookie が Secure で送られていること（開発者ツールで確認）
- [ ] 診断フローが一通り動作すること（start → 5問回答 → score → 結果ページ）
- [ ] ログイン・ログアウト・マイページが動作すること
- [ ] 管理画面 `/admin` に未ログインでアクセスするとログイン画面に飛ぶこと
- [ ] お問い合わせフォームが Formsubmit.co に送信できること（CSP の form-action に formsubmit.co を追加済み）

---

以上を満たしたうえでデプロイしてください。漏れや環境差がある場合は都度このチェックリストを更新することを推奨します。

---

## 9. 保守性（開発・CI）

| 項目 | 内容 |
|------|------|
| CSP・開発用例外 | `api/config/security.php` を編集（`SecurityHeaders` はここだけ参照） |
| テスト | `composer test` … `phpunit.xml` で SQLite メモリ＋固定 `APP_KEY`。DB 非依存で GitHub Actions でも通る |
| コード整形（任意） | `composer lint`（Pint・未整形があると失敗）、`composer lint:fix` で自動修正 |
| CI | リポジトリ直下 `.github/workflows/ci.yml` … `api` で `composer install` → `composer test` |
