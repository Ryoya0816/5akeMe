# ログインできないとき

## 確認すること

1. **ブラウザの URL を APP_URL と合わせる**  
   `api/.env` の `APP_URL=http://localhost:8082` なら、**http://localhost:8082** で開く（`127.0.0.1` は使わない）。

2. **ユーザーがいるか・停止されていないか**  
   - 一般ログイン: `users` テーブルにそのメールのユーザーがいるか。  
   - 管理画面: `is_admin = 1` かつ `is_active = 1` のユーザーでログインする。

3. **パスワード**  
   本番 DB をインポートしただけだと、本番で設定したパスワードのまま。  
   ローカル用に「テスト用パスワード」を設定し直すとよい（下記「テスト用ユーザーを用意する」）。

4. **セッション**  
   - `SESSION_DRIVER=file` なら `api/storage/framework/sessions` が書き込み可能か。  
   - Docker ならコンテナ内で `storage:link` や権限は通常問題ないが、`config:clear` を実行しておく。

---

## テスト用ユーザーを用意する（ローカル）

**方法A: 既存ユーザーのパスワードを「password」に変える**

```bash
cd /Users/tsurumaruryoya/5ake-me
docker compose exec app php artisan tinker
```

tinker で（メールを自分のユーザーに合わせる）:

```php
$u = \App\Models\User::where('email', 'あなたのメール@example.com')->first();
$u->password = \Illuminate\Support\Facades\Hash::make('password');
$u->is_active = true;
$u->save();
exit
```

その後、**パスワード: password** でログインできる。

**方法B: 新規ユーザーを1件作る**

```bash
docker compose exec app php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'テストユーザー',
    'email' => 'test@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password'),
    'email_verified_at' => now(),
    'is_active' => true,
]);
exit
```

**メール: test@example.com / パスワード: password** でログイン。

**方法C: 管理者ユーザーを作る（管理画面用）**

```bash
docker compose exec app php artisan admin:create --email=admin@example.com --name=管理者 --password=password
```

**http://localhost:8082/admin** に **admin@example.com / password** でログイン。

---

## それでもログインできないとき

- ブラウザの開発者ツール → Application（または Storage）→ Cookies で、`localhost:8082` に **5akeme_session**（または `APP_NAME` 由来のセッション Cookie）が保存されているか確認する。  
- ログイン直後に「このアカウントは停止されています」と出る → そのユーザーの `is_active` を 1 にする（上記 tinker の `$u->is_active = true;`）。
