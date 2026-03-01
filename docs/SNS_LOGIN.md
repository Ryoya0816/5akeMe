# SNSログイン（Google / LINE / X）設定ガイド

ログイン・新規登録で **Google**・**LINE**・**X（Twitter）** が利用できます。

**注意:** `.env` で `SNS_LOGIN_ENABLED=false`（未設定時も false）のときは、SNS ボタンを押すと「SNSログインは実装予定です！」と表示され、ログインは行われません。有効にするには `SNS_LOGIN_ENABLED=true` を設定してください。

## クイックチェック（コールバックURLの確認）

**リポジトリのルート（`5ake-me` フォルダ）で** 次を実行してください。

```bash
cd ~/5ake-me
cd api && php artisan sns:check
```

または一発で:

```bash
cd ~/5ake-me/api && php artisan sns:check
```

現在の `APP_URL` に基づいたコールバックURLと、各プロバイダーの設定状況（ID/Secret の有無）が表示されます。表示されたURLを各開発者コンソールにそのまま登録してください。

---

## 必要な環境変数

`.env` に以下を設定してください。未設定のプロバイダーは「このログイン方法は準備中です」となります。

| プロバイダー | 変数 | 説明 |
|-------------|------|------|
| Google | `GOOGLE_CLIENT_ID` | OAuth 2.0 クライアントID |
| Google | `GOOGLE_CLIENT_SECRET` | クライアントシークレット |
| LINE | `LINE_CLIENT_ID` | チャネルID（LINE Login チャネル） |
| LINE | `LINE_CLIENT_SECRET` | チャネルシークレット |
| X | `TWITTER_CLIENT_ID` | OAuth 2.0 Client ID（X Developer） |
| X | `TWITTER_CLIENT_SECRET` | Client Secret |

リダイレクトURLは未設定時、`APP_URL` + 次のパスになります。

- Google: `/auth/google/callback`
- LINE: `/auth/line/callback`
- X: `/auth/twitter/callback`

本番では **APP_URL** を本番のURL（例: `https://example.com`）にしてください。

---

## 各プロバイダーの設定手順

### Google

1. [Google Cloud Console](https://console.cloud.google.com/) でプロジェクトを作成
2. **APIとサービス** → **認証情報** → **認証情報を作成** → **OAuth クライアント ID**
3. アプリケーションの種類: **ウェブアプリケーション**
4. **承認済みのリダイレクト URI** に  
   - ローカル: `http://localhost/auth/google/callback`（または `http://127.0.0.1:8000/auth/google/callback`）  
   - 本番: `https://あなたのドメイン/auth/google/callback`  
   を追加
5. 発行された **クライアントID** と **クライアントシークレット** を `.env` に設定

### LINE

1. [LINE Developers](https://developers.line.biz/) でプロバイダー・チャネルを作成
2. **LINE Login** のチャネルを作成（LINE Login  product を選択）
3. チャネル設定の **LINE Login 設定** で **Callback URL** に  
   - ローカル: `http://localhost/auth/line/callback`  
   - 本番: `https://あなたのドメイン/auth/line/callback`  
   を登録
4. **チャネルID** と **チャネルシークレット** を `.env` の `LINE_CLIENT_ID` / `LINE_CLIENT_SECRET` に設定

### X（Twitter）

1. [X Developer Portal](https://developer.x.com/) でプロジェクト・アプリを作成
2. **User authentication set up** で **OAuth 2.0** を有効化
3. **Callback URI / Redirect URL** に  
   - ローカル: `http://localhost/auth/twitter/callback`  
   - 本番: `https://あなたのドメイン/auth/twitter/callback`  
   を登録
4. **Client ID** と **Client Secret**（OAuth 2.0）を `.env` の `TWITTER_CLIENT_ID` / `TWITTER_CLIENT_SECRET` に設定

※ このアプリでは X（Twitter）に **OAuth 2.0** を使用しています（`config/services.php` の `oauth => 2`）。

---

## 動作確認

1. `.env` に上記を設定し、`php artisan config:clear` を実行
2. ログイン画面 `/login` で「Googleでログイン」「LINEでログイン」「X（Twitter）でログイン」をクリック
3. 各サービスで認証後、アプリのマイページにリダイレクトされればOK

本番（VPS）では、**VPS 上の `.env`** に本番用の Client ID / Secret を入れ、**各開発者コンソールのコールバックURLに本番URL**（例: `https://160.251.214.119/auth/line/callback`）を登録してください。
