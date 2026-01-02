#!/bin/bash

echo "==================================="
echo "Подготовка deployment архива"
echo "==================================="

# Очистка кэша и временных файлов
echo "1. Очистка кэша..."
php artisan cache:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Создание директории для деплоя
echo "2. Создание временной директории..."
DEPLOY_DIR="admin_kassa_deploy_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$DEPLOY_DIR"

# Копирование файлов
echo "3. Копирование файлов..."
rsync -av --progress \
  --exclude='node_modules' \
  --exclude='.git' \
  --exclude='.claude' \
  --exclude='.osp' \
  --exclude='storage/logs/*.log' \
  --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='.env' \
  --exclude='*.log' \
  --exclude='.DS_Store' \
  . "$DEPLOY_DIR/"

# Создание архива
echo "4. Создание ZIP архива..."
zip -r "${DEPLOY_DIR}.zip" "$DEPLOY_DIR" -x "*.git*" > /dev/null

# Очистка
echo "5. Очистка временных файлов..."
rm -rf "$DEPLOY_DIR"

echo ""
echo "✅ Готово!"
echo "Файл: ${DEPLOY_DIR}.zip"
echo "Размер: $(du -h ${DEPLOY_DIR}.zip | cut -f1)"
echo ""
echo "Загрузите этот файл на cPanel и следуйте инструкциям в CPANEL_DEPLOYMENT.md"
echo "==================================="
