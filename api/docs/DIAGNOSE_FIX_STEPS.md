# 診断が「質問の取得に失敗」になるとき（もう一度の手順）

## 出ている問題

1. **localhost:5174 がブロックされる** → 本番で Vite のビルド成果物（/build/）が読まれていない
2. **POST /api/diagnose/start が 500** → サーバ側で例外 or 設定不足

---

## Step 1: Mac でコードをサーバに送る

```bash
cd /Users/tsurumaruryoya/5ake-me
./scripts/deploy-to-vps.sh
```

（再ビルドを聞かれたら、フロントを変えていなければ **N**）

---

## Step 2: VPS で「本番用の表示」にする

**SSH で VPS に入る:**

```bash
ssh ryoya@160.251.214.119
```

**以下を順に実行:**

```bash
# 開発用の hot を消す（あると localhost:5174 を参照し続ける）
rm -f ~/5akeme/api/public/hot

# キャッシュを消す
cd ~/5akeme
docker compose -f docker-compose.production.yml exec -T app php artisan view:clear
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
```

---

## Step 3: 診断の 500 の原因を確認する

**3-1. 一時的にデバッグをオンにする（VPS）**

```bash
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' ~/5akeme/api/.env
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
```

**3-2. ブラウザで診断を試す**

1. http://160.251.214.119/diagnose を開く
2. 「はじめからやり直す」などで診断を開始
3. **F12 → Network** で **`start`** をクリック → **Response** タブを開く

**3-3. Response の JSON を確認**

- **`debug`** がある場合、その内容が原因です。
  - `config(diagnose.fixed_questions) is empty` → 設定が読めていない（Step 4 へ）
  - それ以外の文 → その例外が原因
- **`debug`** が無く `message: "Server Error"` だけ → サーバのログを確認（下記 3-4）

**3-4. ログで確認（VPS）**

```bash
docker compose -f docker-compose.production.yml exec app tail -60 storage/logs/laravel.log
```

`production.ERROR` や `Diagnose` と書いてある行をコピーして共有する。

**3-5. 確認後はデバッグをオフに戻す（VPS）**

```bash
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' ~/5akeme/api/.env
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear
```

---

## Step 4: config(diagnose) が空のとき

**VPS で:**

```bash
# 設定キャッシュを消して、ファイルから再読み込み
docker compose -f docker-compose.production.yml exec -T app php artisan config:clear

# 診断用設定ファイルがあるか確認
ls -la ~/5akeme/api/config/diagnose.php
```

ファイルがあれば、もう一度ブラウザで診断を試す。まだ 500 なら、Step 3 の **start の Response（debug）** か **ログの ERROR 数行** を共有する。

---

## まとめ（実行順）

| 順番 | どこで | やること |
|------|--------|----------|
| 1 | Mac | `./scripts/deploy-to-vps.sh` |
| 2 | VPS | `rm -f ~/5akeme/api/public/hot` → `view:clear` → `config:clear` |
| 3 | VPS | `APP_DEBUG=true` にして `config:clear` |
| 4 | ブラウザ | /diagnose で診断開始 → Network の start の Response を確認 |
| 5 | VPS | 原因が分かったら `APP_DEBUG=false` に戻して `config:clear` |

**start の Response に書いてある `debug` の内容** か **ログの ERROR の数行** が分かると、次の修正を具体的に案内できます。
