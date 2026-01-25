# 5akeMe デプロイ手順書

## 概要

5akeMeを本番環境にデプロイするための手順書です。

---

## 1. 事前準備

### 必要なもの

- [ ] 本番サーバー（VPS/クラウド）
- [ ] ドメイン名
- [ ] SSL証明書（Let's Encrypt推奨）
- [ ] MySQLデータベース
- [ ] メールサーバー（SMTP）

### サーバー要件

- **OS**: Ubuntu 22.04+ / Debian 12+
- **PHP**: 8.3+
- **MySQL**: 8.0+
- **Node.js**: 20+
- **Nginx** または **Apache**
- **Composer**: 2.x

---

## 2. サーバーセットアップ

### 2.1 必要なパッケージをインストール

```bash
# PHP 8.3とエクステンション
sudo apt update
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring \
    php8.3-curl php8.3-zip php8.3-intl php8.3-gd

# Nginx
sudo apt install -y nginx

# MySQL
sudo apt install -y mysql-server

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2.2 アプリケーションのデプロイ

```bash
# アプリケーションディレクトリ作成
sudo mkdir -p /var/www/sakeme
sudo chown -R $USER:$USER /var/www/sakeme

# リポジトリをクローン
cd /var/www/sakeme
git clone https://github.com/Ryoya0816/5akeMe.git .

# apiディレクトリに移動
cd api

# Composerインストール（本番用）
composer install --optimize-autoloader --no-dev

# Node.jsパッケージインストール＆ビルド
npm ci
npm run build
```

---

## 3. 環境設定

### 3.1 .envファイルの設定

```bash
# 本番用.envをコピー
cp .env.production .env

# APP_KEYを生成
php artisan key:generate

# .envを編集
nano .env
```

**必ず変更する項目:**

```env
APP_KEY=base64:xxxx  # 自動生成される
APP_URL=https://your-domain.com
DB_DATABASE=sakeme_production
DB_USERNAME=sakeme_user
DB_PASSWORD=強力なパスワード
SESSION_DOMAIN=your-domain.com
MAIL_HOST=smtp.your-provider.com
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

### 3.2 ディレクトリ権限の設定

```bash
# ストレージとキャッシュの権限
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# ストレージリンク作成
php artisan storage:link
```

### 3.3 データベースのセットアップ

```bash
# MySQLにログイン
sudo mysql

# データベースとユーザー作成
CREATE DATABASE sakeme_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sakeme_user'@'localhost' IDENTIFIED BY '強力なパスワード';
GRANT ALL PRIVILEGES ON sakeme_production.* TO 'sakeme_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# マイグレーション実行
php artisan migrate --force
```

---

## 4. Nginx設定

### 4.1 Nginx設定ファイル作成

```bash
sudo nano /etc/nginx/sites-available/sakeme
```

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/sakeme/api/public;

    # SSL証明書
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    # セキュリティヘッダー
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # アップロードサイズ
    client_max_body_size 10M;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # 静的ファイルのキャッシュ
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 4.2 設定を有効化

```bash
sudo ln -s /etc/nginx/sites-available/sakeme /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 5. SSL証明書（Let's Encrypt）

```bash
# Certbotインストール
sudo apt install -y certbot python3-certbot-nginx

# SSL証明書取得
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# 自動更新の確認
sudo certbot renew --dry-run
```

---

## 6. 本番最適化

### 6.1 キャッシュの最適化

```bash
cd /var/www/sakeme/api

# 設定キャッシュ
php artisan config:cache

# ルートキャッシュ
php artisan route:cache

# ビューキャッシュ
php artisan view:cache

# イベントキャッシュ
php artisan event:cache
```

### 6.2 Makefileコマンド（ローカル用）

```bash
# 本番ビルド
make deploy-build

# キャッシュクリア
make deploy-clear
```

---

## 7. デプロイ更新手順

本番環境を更新する場合：

```bash
cd /var/www/sakeme

# メンテナンスモード有効化
php artisan down

# 最新コードを取得
git pull origin main

# Composer更新
cd api
composer install --optimize-autoloader --no-dev

# アセットビルド
npm ci
npm run build

# マイグレーション
php artisan migrate --force

# キャッシュクリア＆再生成
php artisan config:cache
php artisan route:cache
php artisan view:cache

# メンテナンスモード解除
php artisan up
```

---

## 8. トラブルシューティング

### ログの確認

```bash
# Laravelログ
tail -f /var/www/sakeme/api/storage/logs/laravel.log

# Nginxエラーログ
tail -f /var/log/nginx/error.log

# PHP-FPMログ
tail -f /var/log/php8.3-fpm.log
```

### よくある問題

#### 500エラーが出る
```bash
# 権限を確認
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# キャッシュをクリア
php artisan cache:clear
php artisan config:clear
```

#### 画像がアップロードできない
```bash
# ストレージリンク確認
ls -la public/storage

# なければ作成
php artisan storage:link
```

#### セッションが切れる
- `.env`の`SESSION_DOMAIN`を確認
- `SESSION_SECURE_COOKIE=true`でHTTPS必須

---

## 9. バックアップ

### データベースバックアップ

```bash
# 日次バックアップスクリプト
mysqldump -u sakeme_user -p sakeme_production > /backup/sakeme_$(date +%Y%m%d).sql
```

### ファイルバックアップ

```bash
# アップロードファイルのバックアップ
tar -czf /backup/uploads_$(date +%Y%m%d).tar.gz /var/www/sakeme/api/storage/app/public
```

---

## 10. 監視（推奨）

- **Uptime監視**: UptimeRobot, Pingdom
- **エラー監視**: Sentry, Bugsnag
- **サーバー監視**: Netdata, Prometheus

---

## チェックリスト

デプロイ前の確認事項：

- [ ] `.env`の設定完了
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY`が生成されている
- [ ] データベース接続確認
- [ ] マイグレーション完了
- [ ] ストレージリンク作成済み
- [ ] ディレクトリ権限設定済み
- [ ] SSL証明書設定済み
- [ ] キャッシュ最適化済み
- [ ] メール送信テスト完了
