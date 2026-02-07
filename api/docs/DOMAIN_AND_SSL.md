# お名前.com ドメイン + SSL（HTTPS）設定

お名前.com で取得したドメインを 5akeMe の VPS に向けて、必要なら HTTPS にする手順です。

---

## 1. お名前.com で DNS 設定

1. お名前.com にログイン → **ネームサーバー／DNS 設定** を開く。
2. 対象ドメインの **DNS レコード設定** で以下を追加・確認。

| タイプ | ホスト名 | 値（IPなど） |
|--------|-----------|----------------|
| **A**  | `@`（または空） | `160.251.214.119` |
| **A**  | `www` | `160.251.214.119`（www も使う場合） |

※ ホスト名の表記はお名前.comの画面に合わせてください（例: 空欄＝@、`www`＝www 用）。

3. **反映に 5分〜最大 48 時間** かかることがあります。  
   確認: `dig あなたのドメイン A +short` で `160.251.214.119` が出れば OK。

---

## 2. アプリの設定（ドメインでアクセスする場合）

**api/.env** を編集（VPS 上で）:

```bash
ssh ryoya@160.251.214.119
cd ~/5akeme
nano api/.env
```

変更する項目:

- `APP_URL=https://あなたのドメイン`（HTTPS にする場合は `https://`、まだなら `http://`）
- `SESSION_DOMAIN=.あなたのドメイン`（先頭のドットを付ける。例: `.example.com`）

保存後、キャッシュをクリア:

```bash
docker compose -f docker-compose.production.yml exec app php artisan config:cache
```

---

## 3. nginx でドメインを受け付ける（任意）

今の `production.conf` は `server_name _;` なので **どのホスト名でも** 受け付けます。  
ドメインだけに絞りたい場合は、後で `server_name あなたのドメイン www.あなたのドメイン;` に変更できます（SSL を入れるときにまとめてやるのがおすすめ）。

---

## 4. SSL（HTTPS）を入れる（Let's Encrypt）

**前提:** DNS でドメインが 160.251.214.119 に向いており、**http://あなたのドメイン** でサイトが開けること。

### 4-1. VPS に certbot を入れる

```bash
ssh ryoya@160.251.214.119
sudo apt update
sudo apt install -y certbot
```

### 4-2. いったん 80 番を空ける

Let's Encrypt は 80 番で証明書を取るため、コンテナの 80 を止めます。

```bash
cd ~/5akeme
docker compose -f docker-compose.production.yml stop web
sudo certbot certonly --standalone -d あなたのドメイン -d www.あなたのドメイン
```

メールアドレスと規約への同意を入力。証明書は `/etc/letsencrypt/live/あなたのドメイン/` にできます。

### 4-3. nginx を SSL 対応にする

証明書をコンテナから読めるように、compose でボリュームを足し、nginx 設定を SSL 対応に変更する必要があります。  
手順が長くなるため、ここでは方針だけ。

- **案A:** VPS の `/etc/letsencrypt` を compose の `web` にマウントし、`production.conf` に `listen 443 ssl` と `ssl_certificate` / `ssl_certificate_key` を追加する。
- **案B:** リバースプロキシを VPS 直置きの nginx にして、そこで SSL 終端し、コンテナは 127.0.0.1:80 にする。

まずは **ドメインの A レコード** と **APP_URL / SESSION_DOMAIN** までやって、**http://あなたのドメイン** で表示確認できたら、そのあと SSL を追加するのがおすすめです。

---

## チェックリスト

- [ ] お名前.com で A レコードを 160.251.214.119 に設定した
- [ ] `http://あなたのドメイン` でサイトが開いた
- [ ] api/.env の APP_URL と SESSION_DOMAIN をドメインに合わせた
- [ ] （任意）certbot で証明書取得 → nginx を HTTPS 対応にした
