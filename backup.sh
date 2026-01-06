#!/bin/bash

###############################################################################
# Paynes Backup Script
# Version: 2.0.0
# Description: Скрипт для создания резервной копии БД и файлов
###############################################################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Параметры
BACKUP_ROOT="/var/backups/paynes"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="$BACKUP_ROOT/$DATE"

# Создаем директорию для бэкапов
mkdir -p "$BACKUP_DIR"

log_info "==================================================="
log_info "   Paynes Backup Script v2.0.0"
log_info "   Backup directory: $BACKUP_DIR"
log_info "==================================================="
echo ""

# Загружаем переменные из .env
if [ ! -f .env ]; then
    log_error ".env файл не найден!"
    exit 1
fi

source .env

# 1. Бэкап базы данных
log_info "1/4: Создание бэкапа базы данных..."
mysqldump -h "$DB_HOST" \
          -u "$DB_USERNAME" \
          -p"$DB_PASSWORD" \
          "$DB_DATABASE" \
          --single-transaction \
          --quick \
          --lock-tables=false \
          > "$BACKUP_DIR/database.sql"

# Сжимаем SQL файл
gzip "$BACKUP_DIR/database.sql"
log_success "Бэкап БД создан: database.sql.gz"

# 2. Бэкап .env файла
log_info "2/4: Бэкап .env файла..."
cp .env "$BACKUP_DIR/.env"
log_success ".env файл скопирован"

# 3. Бэкап storage директории (uploaded files)
log_info "3/4: Бэкап файлов из storage..."
tar -czf "$BACKUP_DIR/storage.tar.gz" \
    storage/app/public \
    storage/logs \
    --exclude='storage/framework/cache' \
    --exclude='storage/framework/sessions' \
    --exclude='storage/framework/views' 2>/dev/null || true
log_success "Storage архивирован"

# 4. Создаем информационный файл
log_info "4/4: Создание информации о бэкапе..."
cat > "$BACKUP_DIR/backup_info.txt" << EOF
Paynes Backup Information
=========================
Date: $(date)
Database: $DB_DATABASE
Host: $DB_HOST
App Version: $(php artisan --version)
PHP Version: $(php -r 'echo PHP_VERSION;')

Files:
- database.sql.gz (База данных)
- .env (Конфигурация)
- storage.tar.gz (Файлы storage)
EOF
log_success "Информация сохранена"

# Подсчет размера бэкапа
BACKUP_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)

echo ""
log_success "==================================================="
log_success "   ✅ Бэкап создан успешно!"
log_success "==================================================="
echo ""
log_info "Расположение: $BACKUP_DIR"
log_info "Размер: $BACKUP_SIZE"
echo ""

# Удаление старых бэкапов (старше 30 дней)
log_info "Очистка старых бэкапов (>30 дней)..."
find "$BACKUP_ROOT" -type d -mtime +30 -exec rm -rf {} + 2>/dev/null || true
log_success "Старые бэкапы удалены"

echo ""
log_info "Для восстановления используйте:"
echo "  gunzip $BACKUP_DIR/database.sql.gz"
echo "  mysql -h HOST -u USER -p DATABASE < $BACKUP_DIR/database.sql"
echo ""
