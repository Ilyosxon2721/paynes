#!/bin/bash

# =========================================
# Скрипт подготовки проекта для загрузки на хостинг
# =========================================

echo "🚀 Подготовка проекта Anesi Kassa для загрузки на CPanel..."
echo ""

# Цвета для вывода
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 1. Установка зависимостей Composer (production)
echo -e "${BLUE}[1/6]${NC} Установка Composer зависимостей (production)..."
composer install --optimize-autoloader --no-dev --prefer-dist
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓${NC} Composer зависимости установлены"
else
    echo -e "${RED}✗${NC} Ошибка установки Composer зависимостей"
    exit 1
fi
echo ""

# 2. Установка NPM зависимостей
echo -e "${BLUE}[2/6]${NC} Установка NPM зависимостей..."
npm ci --production=false
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓${NC} NPM зависимости установлены"
else
    echo -e "${RED}✗${NC} Ошибка установки NPM зависимостей"
    exit 1
fi
echo ""

# 3. Сборка frontend
echo -e "${BLUE}[3/6]${NC} Сборка frontend (production)..."
npm run build
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓${NC} Frontend собран"
else
    echo -e "${RED}✗${NC} Ошибка сборки frontend"
    exit 1
fi
echo ""

# 4. Очистка кэша Laravel
echo -e "${BLUE}[4/6]${NC} Очистка кэша Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓${NC} Кэш очищен"
echo ""

# 5. Создание списка файлов для исключения
echo -e "${BLUE}[5/6]${NC} Создание архива проекта..."

# Создаем временный файл со списком исключений
cat > /tmp/exclude_list.txt << 'EOF'
.git
.gitignore
.gitattributes
.editorconfig
node_modules
.env
.env.example
.env.testing
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
tests
phpunit.xml
package-lock.json
.DS_Store
Thumbs.db
*.log
prepare-for-upload.sh
EOF

# Имя архива с датой
ARCHIVE_NAME="anesi-kassa-$(date +%Y%m%d-%H%M%S).tar.gz"

# Создаем архив, исключая файлы из списка
tar -czf "$ARCHIVE_NAME" \
    --exclude-from=/tmp/exclude_list.txt \
    .

# Удаляем временный файл
rm /tmp/exclude_list.txt

if [ -f "$ARCHIVE_NAME" ]; then
    FILE_SIZE=$(du -h "$ARCHIVE_NAME" | cut -f1)
    echo -e "${GREEN}✓${NC} Архив создан: ${YELLOW}$ARCHIVE_NAME${NC} (${FILE_SIZE})"
else
    echo -e "${RED}✗${NC} Ошибка создания архива"
    exit 1
fi
echo ""

# 6. Финальная информация
echo -e "${BLUE}[6/6]${NC} Подготовка завершена!"
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  ✓ Проект готов к загрузке!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}Следующие шаги:${NC}"
echo ""
echo "1. Скачайте архив: ${YELLOW}$ARCHIVE_NAME${NC}"
echo "2. Загрузите на хостинг CPanel (в File Manager)"
echo "3. Распакуйте архив на хостинге"
echo "4. Создайте базу данных MySQL в CPanel"
echo "5. Скопируйте .env.production в .env и настройте:"
echo "   - DB_DATABASE (имя БД)"
echo "   - DB_USERNAME (пользователь БД)"
echo "   - DB_PASSWORD (пароль БД)"
echo "   - APP_URL (ваш домен)"
echo "   - SANCTUM_STATEFUL_DOMAINS (ваш домен)"
echo "6. Сгенерируйте APP_KEY (см. DEPLOYMENT_GUIDE.md)"
echo "7. Запустите миграции: php artisan migrate --force"
echo "8. Заполните данными: php artisan db:seed --force"
echo "9. Настройте Document Root на папку public/"
echo "10. Откройте сайт в браузере!"
echo ""
echo -e "${BLUE}Подробная инструкция: DEPLOYMENT_GUIDE.md${NC}"
echo -e "${BLUE}Быстрый старт: QUICK_DEPLOY.md${NC}"
echo ""
