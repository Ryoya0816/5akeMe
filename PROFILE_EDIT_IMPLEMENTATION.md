# プロフィール編集機能の実装完了

## 実装内容

### ✅ 完了した機能

1. **プロフィール情報編集**
   - ニックネームの変更
   - メールアドレスの変更
   - メール確認機能

2. **アイコン（アバター）アップロード機能**
   - 画像ファイルのアップロード（JPEG、PNG、GIF、WebP、最大2MB）
   - アップロード前のプレビュー表示
   - アバターの削除機能
   - SNSログインの場合はアバター変更不可（SNS側のアバターを使用）

3. **パスワード変更機能**
   - 現在のパスワード確認
   - 新しいパスワード設定

4. **アカウント削除機能**
   - パスワード確認付きアカウント削除

## 実装ファイル

### コントローラー
- `api/app/Http/Controllers/ProfileController.php`
  - アバターアップロード処理を追加
  - アバター削除処理を追加

### リクエストクラス
- `api/app/Http/Requests/ProfileUpdateRequest.php`
  - アバター画像のバリデーション追加

### ビューファイル
- `api/resources/views/profile/edit.blade.php` - プロフィール編集ページ（日本語化済み）
- `api/resources/views/profile/partials/update-password-form.blade.php` - パスワード変更フォーム（日本語化済み）
- `api/resources/views/profile/partials/delete-user-form.blade.php` - アカウント削除フォーム（日本語化済み）

### マイページ
- `api/resources/views/mypage/index.blade.php`
  - プロフィール編集へのリンクを追加

## 動作確認手順

### 1. ストレージのシンボリックリンク作成

Dockerコンテナ内で実行：
```bash
docker compose exec app php artisan storage:link
```

または、ローカルで実行：
```bash
cd api && php artisan storage:link
```

### 2. ストレージディレクトリの確認

```bash
# avatarsディレクトリが作成されているか確認
ls -la api/storage/app/public/avatars

# シンボリックリンクが作成されているか確認
ls -la api/public/storage
```

### 3. 動作確認項目

#### ✅ プロフィール編集ページへのアクセス
- `/mypage` → 「プロフィール編集」メニューをクリック
- `/profile` に直接アクセス

#### ✅ アイコンアップロード
1. 「📷 アイコンを変更」ボタンをクリック
2. 画像ファイルを選択（JPEG、PNG、GIF、WebP）
3. プレビューが表示されることを確認
4. 「保存」ボタンをクリック
5. アバターが更新されることを確認

#### ✅ アイコン削除
1. アバターが設定されている状態で「アイコンを削除」をクリック
2. 確認ダイアログで「OK」をクリック
3. プレビューが🍶に戻ることを確認
4. 「保存」ボタンをクリック
5. アバターが削除されることを確認

#### ✅ プロフィール情報の更新
1. ニックネームを変更して保存
2. メールアドレスを変更して保存（確認メールが送信される）

#### ✅ パスワード変更
1. 現在のパスワードを入力
2. 新しいパスワードを入力
3. 確認パスワードを入力
4. 「保存」ボタンをクリック

## 注意事項

### ストレージ設定
- アバター画像は `storage/app/public/avatars/` に保存されます
- `public/storage` へのシンボリックリンクが必要です
- SNSログインのユーザーは、SNS側のアバターURLを使用するため、削除・変更はできません

### ファイルサイズ制限
- 最大2MBまで
- サポート形式: JPEG、PNG、GIF、WebP

### セキュリティ
- 画像ファイルのみアップロード可能
- ファイルサイズと形式のバリデーションあり
- 古いアバターは自動的に削除されます

## トラブルシューティング

### アバター画像が表示されない
1. シンボリックリンクが作成されているか確認
   ```bash
   docker compose exec app php artisan storage:link
   ```

2. ストレージディレクトリの権限を確認
   ```bash
   chmod -R 775 api/storage
   chmod -R 775 api/bootstrap/cache
   ```

3. `.env`ファイルで`APP_URL`が正しく設定されているか確認

### アップロードエラーが発生する
1. `php.ini`の`upload_max_filesize`と`post_max_size`を確認
2. ストレージディレクトリの書き込み権限を確認

## 次のステップ（オプション）

- [ ] 画像リサイズ機能の追加（サムネイル生成）
- [ ] 画像の最適化（圧縮）
- [ ] 複数のアバター候補から選択する機能
- [ ] アバターのクロップ機能
