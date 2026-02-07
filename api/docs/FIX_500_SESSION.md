# 500 エラー（readonly database）の直し方

VPS に SSH した状態で、以下を **そのまままとめて** 実行してください。

```bash
cd ~/5akeme

# セッション・キャッシュ・キューをすべてファイルに（DB に書き込まない）
sed -i 's/SESSION_DRIVER=database/SESSION_DRIVER=file/' api/.env
sed -i 's/CACHE_STORE=database/CACHE_STORE=file/' api/.env
sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/' api/.env

# 変更を確認
grep -E 'SESSION_DRIVER|CACHE_STORE|QUEUE_CONNECTION' api/.env

# キャッシュ削除（必ず）
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
docker compose -f docker-compose.production.yml exec -T app php artisan cache:clear
docker compose -f docker-compose.production.yml exec -T app php artisan config:cache

# ストレージ権限
docker compose -f docker-compose.production.yml exec -T app sh -c "chmod -R 775 storage bootstrap/cache"
```

その後、ブラウザで http://160.251.214.119/top を開き直す。

---

## まだ 500 のとき

ログの **先頭**（例外メッセージ）を確認する:

```bash
docker compose -f docker-compose.production.yml exec app tail -200 storage/logs/laravel.log | head -80
```

`PDOException` や `attempt to write` などの **1〜2行目** をコピーして共有する。
