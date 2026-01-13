#!/bin/bash

###############################################################################
# Production Deployment Script for VPS
# Project: Paynes - Admin Kassa
# Description: Автоматический деплой на VPS (Ubuntu/Debian)
###############################################################################

set -e # Остановить выполнение при ошибке

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Конфигурация
PROJECT_PATH="/home/deploy/admin_kassa"
BRANCH="main"

# Функции логирования
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

# Проверка прав
if [ "$EUID" -eq 0 ]; then
    log_error "Не запускайте скрипт от имени root!"
    exit 1
fi

log_info "==================================================="
log_info "   Paynes VPS Deployment Script"
log_info "==================================================="
echo ""

# Переходим в директорию проекта
cd "$PROJECT_PATH" || {
    log_error "Не удалось перейти в директорию проекта: $PROJECT_PATH"
    exit 1
}

# Шаг 1: Режим обслуживания
log_info "Шаг 1/11: Включение режима обслуживания..."
php artisan down || log_warning "Режим обслуживания уже включен"
log_success "Режим обслуживания включен"

# Шаг 2: Получение изменений из Git
log_info "Шаг 2/11: Получение изменений из Git..."
git fetch origin "$BRANCH"
git reset --hard "origin/$BRANCH"
log_success "Изменения получены"

# Шаг 3: Установка backend зависимостей
log_info "Шаг 3/11: Установка Composer зависимостей..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
log_success "Composer зависимости установлены"

# Шаг 4: Установка frontend зависимостей
log_info "Шаг 4/11: Установка npm зависимостей..."
npm ci --production
log_success "npm зависимости установлены"

# Шаг 5: Сборка frontend
log_info "Шаг 5/11: Сборка frontend..."
npm run build
log_success "Frontend собран"

# Шаг 6: Проверка .env
log_info "Шаг 6/11: Проверка .env..."
if [ ! -f .env ]; then
    log_error ".env файл не найден!"
    php artisan up
    exit 1
fi

if grep -q "APP_ENV=local" .env; then
    log_error "APP_ENV должен быть production!"
    php artisan up
    exit 1
fi

if grep -q "APP_DEBUG=true" .env; then
    log_error "APP_DEBUG должен быть false!"
    php artisan up
    exit 1
fi
log_success ".env проверен"

# Шаг 7: Миграции
log_info "Шаг 7/11: Выполнение миграций..."
php artisan migrate --force
log_success "Миграции выполнены"

# Шаг 8: Очистка кешей
log_info "Шаг 8/11: Очистка старых кешей..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
log_success "Кеши очищены"

# Шаг 9: Создание новых кешей
log_info "Шаг 9/11: Создание новых кешей..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
log_success "Кеши созданы"

# Шаг 10: Права доступа
log_info "Шаг 10/11: Проверка прав доступа..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
log_success "Права установлены"

# Шаг 11: Перезапуск воркеров
log_info "Шаг 11/11: Перезапуск queue воркеров..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart admin_kassa_worker:*
    log_success "Воркеры перезапущены"
else
    log_warning "Supervisor не установлен, пропускаем перезапуск воркеров"
fi

# Выключаем режим обслуживания
log_info "Выключение режима обслуживания..."
php artisan up
log_success "Режим обслуживания выключен"

# Финальная проверка
log_info "Финальная проверка..."
php artisan --version

echo ""
log_success "==================================================="
log_success "   ✅ Деплой завершен успешно!"
log_success "==================================================="
echo ""
log_info "Полезные команды:"
echo "  Логи Laravel:     tail -f storage/logs/laravel.log"
echo "  Логи Nginx:       sudo tail -f /var/log/nginx/admin_kassa_error.log"
echo "  Логи Worker:      tail -f storage/logs/worker.log"
echo "  Статус воркеров:  sudo supervisorctl status"
echo ""
