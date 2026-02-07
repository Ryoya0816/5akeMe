# 診断開始 API が 500 のとき

POST /api/diagnose/start が 500 になる場合の確認手順です。

---

## 1. サーバでエラー内容を確認（VPS）

```bash
cd ~/5akeme
docker compose -f docker-compose.production.yml exec app tail -80 storage/logs/laravel.log
```

**「diagnose」や「Diagnose」や「createSession」を含む行**、および **production.ERROR** の直後の例外メッセージを確認する。

---

## 2. よくある原因と対処

| 原因 | 対処 |
|------|------|
| **config('diagnose') が空** | `config:clear` してから再度リクエスト。config/diagnose.php がサーバに存在するか確認。 |
| **DB 接続エラー** | .env の DB_CONNECTION=mysql, DB_HOST=db とコンテナの db が動いているか確認。 |
| **Python API は start では使わない** | /api/diagnose/start は PHP の DiagnoseService のみ。score で 500 のときは Python を疑う。 |

---

## 3. 設定ファイルの存在確認（VPS）

```bash
ls -la ~/5akeme/api/config/diagnose.php
```

ファイルがあれば、設定キャッシュをクリアして再試行:

```bash
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
```

---

## 4. 再現してログを取る

1. ブラウザで http://160.251.214.119/diagnose を開く
2. すぐに VPS で:  
   `docker compose -f docker-compose.production.yml exec app tail -100 storage/logs/laravel.log`
3. 出た **ERROR や Exception の行** をコピーして共有する
