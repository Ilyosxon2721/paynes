#!/bin/bash

###############################################################################
# Paynes Update Script
# Version: 2.0.0
# Description: Скрипт для обновления приложения без полного redeployment
###############################################################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_info "==================================================="
log_info "   Paynes Update Script v2.0.0"
log_info "==================================================="
echo ""

# Создаем бэкап перед обновлением
log_info "Создание бэкапа..."
BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Бэкап базы данных
if command -v mysqldump &> /dev/null; then
    log_info "Бэкап базы данных..."
    source .env
    mysqldump -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_DIR/database.sql"
    log_success "Бэкап БД создан: $BACKUP_DIR/database.sql"
fi

# Бэкап .env
cp .env "$BACKUP_DIR/.env"
log_success "Бэкап .env создан"

# Включаем режим обслуживания
log_info "Включение режима обслуживания..."
php artisan down

# Получаем новый код (если используется git)
if [ -d .git ]; then
    log_info "Обновление кода из git..."
    git pull origin main
    log_success "Код обновлен"
fi

# Обновляем зависимости
log_info "Обновление backend зависимостей..."
composer install --optimize-autoloader --no-dev --no-interaction

log_info "Обновление frontend зависимостей..."
npm ci --production=false

log_info "Сборка frontend..."
npm run build

# Миграции
log_info "Проверка новых миграций..."
read -p "Запустить миграции? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    log_success "Миграции выполнены"
fi

# Очистка и кеширование
log_info "Обновление кешей..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Права доступа
chmod -R 775 storage bootstrap/cache

# Выключаем режим обслуживания
log_info "Выключение режима обслуживания..."
php artisan up

echo ""
log_success "==================================================="
log_success "   ✅ Обновление завершено успешно!"
log_success "==================================================="
echo ""
log_info "Бэкап сохранен в: $BACKUP_DIR"
echo ""
