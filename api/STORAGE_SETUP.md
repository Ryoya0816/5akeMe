# ストレージ設定ガイド

## 問題：アイコン画像が表示されない

アイコン画像が表示されない主な原因は、**ストレージのシンボリックリンクが作成されていない**ことです。

## 解決方法

### 1. Dockerコンテナ内でシンボリックリンクを作成

```bash
docker compose exec app php artisan storage:link
```

### 2. ローカルで実行する場合

```bash
cd api
php artisan storage:link
```

### 3. 手動でシンボリックリンクを作成

```bash
cd api
ln -s ../storage/app/public public/storage
```

## 確認方法

シンボリックリンクが正しく作成されているか確認：

```bash
ls -la api/public/storage
```

以下のように表示されればOK：
```
lrwxr-xr-x  storage -> ../storage/app/public
```

## トラブルシューティング

### シンボリックリンクが作成できない場合

1. **権限の問題**
   ```bash
   sudo docker compose exec app php artisan storage:link
   ```

2. **既存のファイル/ディレクトリがある場合**
   ```bash
   rm -rf api/public/storage
   docker compose exec app php artisan storage:link
   ```

3. **ストレージディレクトリの権限確認**
   ```bash
   chmod -R 775 api/storage
   chmod -R 775 api/bootstrap/cache
   ```

## アバター画像の保存先

- **保存先**: `api/storage/app/public/avatars/`
- **公開URL**: `http://localhost:8082/storage/avatars/{filename}`
- **データベース保存値**: `/storage/avatars/{filename}` または `http://localhost:8082/storage/avatars/{filename}`

## 注意事項

- アバター画像は最大2MBまで
- 対応形式: JPEG、PNG、GIF、WebP
- SNSログインのユーザーは、SNS側のアバターURLを使用するため変更不可
