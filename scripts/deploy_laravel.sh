#!/bin/bash

# Enter html directory
cd /var/www/kotonaru-web-user/

# Create cache and chmod folders
mkdir -p /var/www/kotonaru-web-user/bootstrap/cache
mkdir -p /var/www/kotonaru-web-user/storage/framework/sessions
mkdir -p /var/www/kotonaru-web-user/storage/framework/views
mkdir -p /var/www/kotonaru-web-user/storage/framework/cache
mkdir -p /var/www/kotonaru-web-user/public/files/

# Install dependencies
export COMPOSER_ALLOW_SUPERUSER=1
composer install -d /var/www/kotonaru-web-user/

# Copy configuration from /var/www/.env, see README.MD for more information
#cp /var/www/.env /var/www/html/gscsweb/gscs/.env

# Migrate all tables
php /var/www/kotonaru-web-user/artisan migrate

# Clear any previous cached views
php /var/www/kotonaru-web-user/artisan config:clear
php /var/www/kotonaru-web-user/artisan cache:clear
php /var/www/kotonaru-web-user/artisan view:clear

# Optimize the application
php /var/www/kotonaru-web-user/artisan config:cache
php /var/www/kotonaru-web-user/artisan optimize
php /var/www/kotonaru-web-user/artisan optimize:clear
#php /var/www/kotonaru-web-user/artisan route:cache

# Change rights
sudo chmod 777 -R /var/www/kotonaru-web-user/bootstrap/cache
sudo chmod 777 -R /var/www/kotonaru-web-user/storage
sudo chmod 777 -R /var/www/kotonaru-web-user/public/files/

# Bring up application
#php /var/www/kotonaru-web-user/artisan up
