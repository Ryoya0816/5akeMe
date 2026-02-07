# 5akeMe VPS デプロイ引き継ぎ（ConoHa / Ubuntu 24.04 / 1GB）

## VPS基本情報

| 項目 | 値 |
|------|-----|
| Provider | ConoHa VPS |
| OS | Ubuntu 24.04.3 LTS |
| VPS IPv4 | **160.251.214.119** |
| 作業ユーザー | ryoya（sudo可） |
| SSH | Mac → VPS 鍵ログイン（ssh-copy-id済み） |
| 目標 | Docker Compose で 5akeMe を本番デプロイ（コスパ最重視・アクセス少） |

**注意:** 過去IP `163.44.99.223` は無効。必ず **160.251.214.119** を使う。

---

## SSH接続（Mac）

```bash
ssh ryoya@160.251.214.119
```

root で入る必要はない（鍵ログイン確認後に root ログインは禁止する）。

---

## すでに完了済みの作業

- [x] swap 有効化済み（zram 併用で Swap 合計 4G 表示）
- [x] sshd 設定調整（PermitRootLogin / PasswordAuthentication を一時的に触ったが、現在は鍵ログイン OK）
- [x] ryoya ユーザー作成・sudo 付与済み
- [x] ConoHa 側セキュリティグループ対応済み（160.251.214.119 で SSH 疎通）

---

## 次にやること（優先順位）

### 1) SSH 設定の最終セキュア化

**※ 鍵ログインで `ssh ryoya@160.251.214.119` が成功していることを確認してから実施すること。**

- root ログイン禁止
- パスワード認証禁止（鍵のみ）

**手順:**

```bash
# VPS に ryoya でログインした状態で

# 既存設定の確認（上書きされていないか）
sudo cat /etc/ssh/sshd_config | grep -E "^(PermitRootLogin|PasswordAuthentication|PubkeyAuthentication)"
ls -la /etc/ssh/sshd_config.d/

# 必要なら /etc/ssh/sshd_config を編集
sudo nano /etc/ssh/sshd_config
# 以下を設定（既存行があれば変更、なければ追加）
#   PermitRootLogin no
#   PasswordAuthentication no
#   PubkeyAuthentication yes

# /etc/ssh/sshd_config.d/ 内の *.conf で上書きしていないか確認
# 問題なければ ssh を再起動
sudo systemctl restart ssh
```

**検証（必ず別ターミナルで、ログアウトする前に）:**

- `ssh root@160.251.214.119` → **失敗**すること
- `ssh ryoya@160.251.214.119` → **成功**すること

---

### 2) Docker / Compose 導入

```bash
sudo apt update && sudo apt upgrade -y
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker ryoya
sudo apt install -y docker-compose-plugin
```

**ログアウトして再ログイン**するか、`newgrp docker` のあと:

```bash
docker ps
```

がエラーなく通れば OK。

---

### 3) nginx 疎通テスト（80番）

最小 compose で nginx が動くか確認する。

```bash
mkdir -p ~/nginx-test && cd ~/nginx-test
echo 'services:
  web:
    image: nginx:alpine
    ports:
      - "80:80"
' > docker-compose.yml
docker compose up -d
```

ブラウザで **http://160.251.214.119** を開き、nginx のデフォルトページが見えれば OK。

```bash
docker compose down
cd ..
```

---

### 4) 5akeMe 本番 compose（1GB 前提で軽量化）

- **構成:** nginx（reverse proxy）、Laravel php-fpm（api）、FastAPI（uvicorn workers=1）、MySQL 8（メモリ制限）
- **Vite ビルド:** サーバでは行わず、ローカルで `npm run build` → 成果物をデプロイする。

リポジトリの **docker-compose.production.yml** を使用する手順は、同梱の **api/docs/** を参照。

- 今すぐやる手順: [DEPLOY_NOW.md](./DEPLOY_NOW.md)
- チェックリスト: [DEPLOY_CHECKLIST.md](./DEPLOY_CHECKLIST.md)
- 詳細: [DEPLOY_CONOHA_5AKEME.md](./DEPLOY_CONOHA_5AKEME.md)

---

## 技術スタック

- Backend: PHP 8.3 / Laravel 12 / Breeze / Socialite / Filament
- Front: Vite 7 / Tailwind / Alpine.js
- AI: Python / FastAPI / scikit-learn / pandas
- DB: MySQL 8.0
- Infra: Docker Compose / Nginx / Node 20（ビルドはローカル）

---

## 注意点

- 1GB のため MySQL 同居は swap 前提。厳しければ SQLite や外部 DB も検討。
- 本番では `APP_DEBUG=false`、`.env` の DB パスワード・APP_KEY を必ず設定すること。
- **Git に上げてはいけないもの**・**本番と検証の分離**は [GIT_AND_DEPLOY_CHECKLIST.md](./GIT_AND_DEPLOY_CHECKLIST.md) を参照。

---

## 参考: SSH とは（初回読む人向け）

**SSH** = サーバーに遠隔ログインするための仕組み。暗号化された通信で、自分の PC から VPS 上でコマンドを実行できる。

- 初回接続時は「ホストの確認」で `yes` を入力する。
- ログアウトは `exit` または `Ctrl+D`。

### よくあるエラー

| メッセージ | 対処 |
|------------|------|
| Connection refused | VPS が起動しているか、ファイアウォールで 22 番が空いているか確認。 |
| Connection timed out | インターネット・IP（160.251.214.119）の打ち間違い・VPS 起動確認。 |
| Permission denied (publickey) | 鍵が正しいか、`ssh ryoya@160.251.214.119` で鍵ログインできるか確認。 |
