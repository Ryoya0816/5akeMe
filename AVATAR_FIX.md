# アイコン画像が変更されない問題の修正

## 問題の原因

1. **ストレージのシンボリックリンクが作成されていない**
   - `public/storage` → `storage/app/public` へのシンボリックリンクが必要

2. **アバターの変更が保存されない可能性**
   - `isDirty()`がアバターの変更を検出しない場合がある

## 修正内容

### 1. コントローラーの修正 ✅
- アバター変更時は常に保存するように修正
- ログを追加してデバッグしやすく

### 2. ビューの修正 ✅
- アバター表示時に`asset()`を使用（HTTP URLの場合はそのまま）
- SNSログインのアバター（HTTP URL）とローカルアバター（/storage/）を区別

## 必須の設定手順

### シンボリックリンクの作成

**Dockerコンテナ内で実行：**
```bash
docker compose exec app php artisan storage:link
```

**またはローカルで実行：**
```bash
cd api
php artisan storage:link
```

### 確認方法

```bash
# シンボリックリンクが作成されているか確認
ls -la api/public/storage

# 以下のように表示されればOK
# lrwxr-xr-x  storage -> ../storage/app/public
```

## 動作確認

1. `/profile` にアクセス
2. 「📷 アイコンを変更」をクリック
3. 画像ファイルを選択
4. プレビューが表示されることを確認
5. 「保存」をクリック
6. ページがリロードされ、新しいアイコンが表示されることを確認

## トラブルシューティング

### まだ画像が表示されない場合

1. **シンボリックリンクの確認**
   ```bash
   ls -la api/public/storage
   ```

2. **ストレージディレクトリの確認**
   ```bash
   ls -la api/storage/app/public/avatars
   ```

3. **ログの確認**
   ```bash
   docker compose exec app tail -f storage/logs/laravel.log
   ```

4. **ブラウザのコンソールでエラー確認**
   - F12キーで開発者ツールを開く
   - Consoleタブでエラーを確認
   - Networkタブで画像のリクエストが404になっていないか確認

### 画像が404エラーになる場合

- シンボリックリンクが作成されていない可能性が高い
- `php artisan storage:link` を実行

### データベースには保存されているが表示されない場合

- ブラウザのキャッシュをクリア（Ctrl+Shift+R または Cmd+Shift+R）
- 画像のパスが正しいか確認（`/storage/avatars/...`）
