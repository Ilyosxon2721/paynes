#!/bin/bash

###############################################################################
# Laravel Forge Deployment Script
# Project: Paynes - Admin Kassa
# Оптимизированный скрипт деплоя для Laravel Forge
###############################################################################

cd /home/forge/yourdomain.com

# Включаем режим обслуживания
$FORGE_PHP artisan down || true

# Получаем изменения из Git
git pull origin $FORGE_SITE_BRANCH

# Устанавливаем Composer зависимости
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Перезагружаем PHP-FPM
( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

# Устанавливаем NPM зависимости и собираем фронтенд
if [ -f package.json ]; then
    echo "Installing npm dependencies..."
    npm ci --production

    echo "Building frontend assets..."
    export NODE_OPTIONS="--max-old-space-size=2048"
    npm run build
fi

# Выполняем миграции
if [ -f artisan ]; then
    echo "Running migrations..."
    $FORGE_PHP artisan migrate --force
fi

# Очищаем старые кеши
echo "Clearing caches..."
$FORGE_PHP artisan config:clear
$FORGE_PHP artisan route:clear
$FORGE_PHP artisan view:clear
$FORGE_PHP artisan cache:clear

# Создаем новые кеши
echo "Caching configuration..."
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan route:cache
$FORGE_PHP artisan view:cache
$FORGE_PHP artisan optimize

# Устанавливаем права доступа
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Перезапускаем queue workers (если настроены)
if [ -d "/etc/supervisor/conf.d" ]; then
    echo "Restarting queue workers..."
    sudo supervisorctl restart all
fi

# Выключаем режим обслуживания
$FORGE_PHP artisan up

echo "✅ Deployment completed successfully!"
