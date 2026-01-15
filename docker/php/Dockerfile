FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip \
 && docker-php-ext-install pdo_mysql

WORKDIR /var/www/html
