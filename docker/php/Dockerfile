FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip libicu-dev \
 && docker-php-ext-install pdo_mysql intl zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Upload size limits
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/zz-custom.ini
