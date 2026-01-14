#!/bin/bash
# Laravel設定キャッシュをクリアするスクリプト

echo "Clearing Laravel configuration cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Configuration cache cleared!"
echo ""
echo "Current database configuration:"
php artisan config:show database.default
php artisan config:show database.connections.mysql.host
php artisan config:show database.connections.mysql.database
