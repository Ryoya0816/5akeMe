# 接続できないときのチェックリスト

「サイトに接続できなかった」ときに、原因を切り分ける手順です。

---

## 今すぐやること（ポート 80 / HTTP が NG のとき）

**原因の多くは ConoHa のファイアウォールで 80 番が閉じていることです。**

1. **ConoHa by GMO** にログイン
2. **サーバ** → 対象 VPS（160.251.214.119 のやつ）をクリック
3. **ファイアウォール** または **セキュリティグループ** の設定を開く
4. **インバウンド**（外→中への通信）にルールを追加:
   - **TCP** / **80** / **0.0.0.0/0**（または 任意） で **許可**
   - 同様に **TCP 22**（SSH）も許可されていなければ追加
5. 保存して 1〜2 分待ってから、もう一度 `./scripts/check-vps-connection.sh` を実行

---

## 1. Mac から一括チェック

```bash
cd /path/to/5ake-me
./scripts/check-vps-connection.sh
```

Ping・80番・HTTP・SSH を順に確認します。

---

## 2. どこで接続できないか

| 症状 | 見る場所 |
|------|----------|
| **ブラウザで http://160.251.214.119 が開かない** | 下記 3〜5 |
| **お名前.com のドメインで開かない** | DNS 反映 + 下記 3〜5 |
| **SSH できない** | 下記 6 |

---

## 3. ConoHa のファイアウォール（80 番が塞がっている）

ConoHa のコントロールパネルで、対象 VPS の **ファイアウォール** を確認します。

- **インバウンド** で **TCP 80**（HTTP）を **許可** するルールがあるか。
- デフォルトで「全許可」でない場合、80 を追加しないと外から Web に届きません。

同様に **22**（SSH）も許可されているか確認してください。

---

## 4. VPS 上で Docker が動いているか

SSH で入れる場合:

```bash
ssh ryoya@160.251.214.119
cd ~/5akeme
docker compose -f docker-compose.production.yml ps
```

**期待する状態:** `web`（nginx）, `app`, `python`, `db` が **Up**。

- どれか **Exit** や **Restarting** → ログを確認（下記 5）。
- コンテナが無い → `docker compose -f docker-compose.production.yml up -d` で起動。

---

## 5. コンテナのログでエラー確認

```bash
# 全体のログ（直近）
docker compose -f docker-compose.production.yml logs --tail=50

# nginx（web）だけ
docker compose -f docker-compose.production.yml logs web --tail=30

# Laravel（app）だけ
docker compose -f docker-compose.production.yml logs app --tail=30

# Laravel アプリログ
docker compose -f docker-compose.production.yml exec app tail -50 storage/logs/laravel.log
```

**よくある原因:**

- **502 Bad Gateway** → `app` が落ちている or PHP が応答していない。`logs app` と `storage/logs/laravel.log` を確認。
- **Connection refused (DB)** → `db` が起動していない or `.env` の DB パスワードが MySQL と一致していない。
- **No such file (nginx)** → `~/5akeme/api` や `production.conf` のパスが違う。compose の `volumes` を確認。

---

## 6. VPS 内で 80 番が聞いているか

SSH で VPS に入った状態で:

```bash
sudo ss -tlnp | grep :80
# または
curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1/
```

- `ss` で **:80** が表示されない → nginx コンテナが起動していないか、ポートマッピングが外れている。
- `curl 127.0.0.1` が 200 → VPS 内では動いているので、**ConoHa のファイアウォールで 80 が塞がっている**可能性が高いです。

---

## 7. 再起動してやり直す

```bash
cd ~/5akeme
docker compose -f docker-compose.production.yml down
docker compose -f docker-compose.production.yml up -d
docker compose -f docker-compose.production.yml ps
```

---

## まとめ（確認順）

1. **./scripts/check-vps-connection.sh** で Mac から疎通確認。
2. **ConoHa** で 80 番（と 22 番）が許可されているか確認。
3. **VPS** で `docker compose -f docker-compose.production.yml ps` でコンテナがすべて Up か確認。
4. **ログ** で 502 / DB エラー / パス違いがないか確認。

どこまでできたか・どのステップで NG になったかが分かると、原因をさらに絞り込みやすいです。
