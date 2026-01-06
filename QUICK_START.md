# 🚀 Быстрый старт - Paynes Production

## 📌 Для быстрого развертывания на production сервере

### Вариант 1: Автоматическое развертывание (Рекомендуется)

```bash
# 1. Загрузить проект на сервер
cd /var/www/html/paynes

# 2. Дать права на выполнение скриптов
chmod +x deploy.sh update.sh backup.sh

# 3. Запустить deploy скрипт
./deploy.sh
```

Скрипт автоматически выполнит все необходимые шаги.

### Вариант 2: Ручное развертывание

```bash
# 1. Установить зависимости
composer install --optimize-autoloader --no-dev
npm install
npm run build

# 2. Настроить .env
cp .env.production.example .env
nano .env  # Настроить параметры
php artisan key:generate

# 3. Обновить public/.htaccess (строка 2)
nano public/.htaccess
# Изменить: SetEnv APP_ENV local → SetEnv APP_ENV production

# 4. Миграции и seeding
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force
php artisan db:seed --class=AdminUserSeeder --force

# 5. Оптимизация
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. Права доступа
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## ✅ Проверка работоспособности

### 1. Health Check API
```bash
curl https://your-domain.com/api/health
```

Должен вернуть:
```json
{
  "status": "ok",
  "services": {
    "database": "connected",
    "cache": "connected"
  }
}
```

### 2. Вход в систему

Откройте в браузере: `https://your-domain.com`

**Логин**: `admin`
**Пароль**: `admin123`

⚠️ **ВАЖНО**: Сразу смените пароль после первого входа!

## 🔧 Настройка .env (КРИТИЧНО!)

Обязательные параметры для production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-actual-domain.com

DB_CONNECTION=mysql
DB_HOST=83.69.139.168
DB_DATABASE=uz123_paynes
DB_USERNAME=uz123_SuperAdmin
DB_PASSWORD=Bankir2721
```

## 📋 После развертывания

### Обязательно:
1. ✅ Проверить, что сайт открывается по HTTPS
2. ✅ Войти как админ и сменить пароль
3. ✅ Создать первого кассира
4. ✅ Провести тестовую смену
5. ✅ Проверить все функции

### Рекомендуется:
1. Настроить автоматические бэкапы
2. Настроить мониторинг логов
3. Установить Firewall правила
4. Настроить email уведомления

## 🆘 Решение проблем

### Сайт не открывается?
```bash
# Проверить веб-сервер
sudo systemctl status apache2  # или nginx

# Проверить логи Laravel
tail -f storage/logs/laravel.log

# Проверить права
ls -la storage bootstrap/cache
```

### Ошибки в логах?
```bash
# Очистить все кеши
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Пересоздать кеши
php artisan config:cache
php artisan route:cache
php artisan optimize
```

### База данных не подключается?
```bash
# Проверить подключение
php artisan migrate:status

# Проверить данные в .env
cat .env | grep DB_
```

## 📞 Поддержка

**Документация**: [DEPLOYMENT.md](DEPLOYMENT.md)
**Checklist**: [PRODUCTION_CHECKLIST.md](PRODUCTION_CHECKLIST.md)

---

🎉 **Готово! Система развернута и готова к работе!**
