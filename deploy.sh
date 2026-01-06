#!/bin/bash

###############################################################################
# Paynes Production Deployment Script
# Version: 2.0.0
# Description: Автоматическое развертывание приложения в production
###############################################################################

set -e # Остановить выполнение при ошибке

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Функция для логирования
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

# Проверка запуска от имени правильного пользователя
if [ "$EUID" -eq 0 ]; then
    log_error "Не запускайте скрипт от имени root! Используйте sudo только где необходимо."
    exit 1
fi

log_info "==================================================="
log_info "   Paynes Production Deployment Script v2.0.0"
log_info "==================================================="
echo ""

# Шаг 1: Проверка требований
log_info "Шаг 1/12: Проверка требований системы..."

if ! command -v php &> /dev/null; then
    log_error "PHP не установлен!"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    log_error "Composer не установлен!"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    log_error "NPM не установлен!"
    exit 1
fi

PHP_VERSION=$(php -r 'echo PHP_VERSION;')
log_success "PHP версия: $PHP_VERSION"

COMPOSER_VERSION=$(composer --version | head -n1)
log_success "$COMPOSER_VERSION"

NODE_VERSION=$(node --version)
log_success "Node.js версия: $NODE_VERSION"

# Шаг 2: Режим обслуживания
log_info "Шаг 2/12: Включение режима обслуживания..."
php artisan down || log_warning "Режим обслуживания уже включен"
log_success "Режим обслуживания включен"

# Шаг 3: Установка backend зависимостей
log_info "Шаг 3/12: Установка backend зависимостей..."
composer install --optimize-autoloader --no-dev --no-interaction
log_success "Backend зависимости установлены"

# Шаг 4: Установка frontend зависимостей
log_info "Шаг 4/12: Установка frontend зависимостей..."
npm ci --production=false
log_success "Frontend зависимости установлены"

# Шаг 5: Сборка frontend
log_info "Шаг 5/12: Сборка frontend assets..."
npm run build
log_success "Frontend собран"

# Шаг 6: Проверка .env
log_info "Шаг 6/12: Проверка конфигурации .env..."
if [ ! -f .env ]; then
    log_error ".env файл не найден! Скопируйте .env.production.example и настройте его."
    php artisan up
    exit 1
fi

# Проверяем критичные настройки
if grep -q "APP_ENV=local" .env; then
    log_error "APP_ENV=local в .env! Должен быть APP_ENV=production"
    php artisan up
    exit 1
fi

if grep -q "APP_DEBUG=true" .env; then
    log_error "APP_DEBUG=true в .env! Должен быть APP_DEBUG=false"
    php artisan up
    exit 1
fi

log_success "Конфигурация .env проверена"

# Шаг 7: Генерация APP_KEY если не установлен
log_info "Шаг 7/12: Проверка APP_KEY..."
if grep -q "APP_KEY=$" .env || ! grep -q "APP_KEY=" .env; then
    log_warning "APP_KEY не установлен, генерируем новый..."
    php artisan key:generate --force
    log_success "APP_KEY сгенерирован"
else
    log_success "APP_KEY уже установлен"
fi

# Шаг 8: Миграции базы данных
log_info "Шаг 8/12: Выполнение миграций базы данных..."
read -p "Запустить миграции БД? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    log_success "Миграции выполнены"
else
    log_warning "Миграции пропущены"
fi

# Шаг 9: Seeding (опционально)
log_info "Шаг 9/12: Seeding базы данных..."
read -p "Запустить seeding (только для первого развертывания)? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --class=RolesAndPermissionsSeeder --force
    php artisan db:seed --class=AdminUserSeeder --force
    php artisan db:seed --class=ExchangeRatesSeeder --force
    log_success "Seeding выполнен"
else
    log_warning "Seeding пропущен"
fi

# Шаг 10: Очистка кешей
log_info "Шаг 10/12: Очистка старых кешей..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
log_success "Кеши очищены"

# Шаг 11: Создание новых кешей
log_info "Шаг 11/12: Создание новых кешей..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
log_success "Кеши созданы"

# Шаг 12: Установка прав доступа
log_info "Шаг 12/12: Установка прав доступа..."
chmod -R 775 storage bootstrap/cache
log_success "Права доступа установлены"

# Выключаем режим обслуживания
log_info "Выключение режима обслуживания..."
php artisan up
log_success "Режим обслуживания выключен"

# Финальная проверка
log_info "Финальная проверка..."
php artisan --version

echo ""
log_success "==================================================="
log_success "   ✅ Развертывание завершено успешно!"
log_success "==================================================="
echo ""
log_info "Следующие шаги:"
echo "1. Проверьте сайт в браузере"
echo "2. Проверьте логи: tail -f storage/logs/laravel.log"
echo "3. Протестируйте основные функции"
echo "4. Смените пароль администратора"
echo ""
log_warning "⚠️  Не забудьте обновить public/.htaccess (SetEnv APP_ENV production)"
echo ""
