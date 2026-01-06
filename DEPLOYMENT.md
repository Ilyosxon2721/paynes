# 🚀 Руководство по развертыванию Paynes в Production

## 📋 Предварительные требования

- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Node.js >= 18.x
- NPM >= 9.x
- SSL сертификат (для HTTPS)

## 🔧 Шаг 1: Подготовка сервера

### 1.1 Установка зависимостей на сервере

```bash
# Обновить систему
sudo apt update && sudo apt upgrade -y

# Установить PHP и расширения
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
  php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-redis -y

# Установить Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Установить Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

### 1.2 Создание базы данных

```sql
CREATE DATABASE uz123_paynes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'uz123_SuperAdmin'@'localhost' IDENTIFIED BY 'Bankir2721';
GRANT ALL PRIVILEGES ON uz123_paynes.* TO 'uz123_SuperAdmin'@'localhost';
FLUSH PRIVILEGES;
```

## 📦 Шаг 2: Загрузка проекта

```bash
# Клонировать или загрузить проект на сервер
cd /var/www/html
git clone <repository-url> paynes
# ИЛИ загрузить через FTP/SFTP

cd paynes
```

## ⚙️ Шаг 3: Установка зависимостей

```bash
# Backend зависимости
composer install --optimize-autoloader --no-dev

# Frontend зависимости и сборка
npm install
npm run build
```

## 🔐 Шаг 4: Конфигурация .env

```bash
# Копировать production шаблон
cp .env.production .env

# Сгенерировать APP_KEY
php artisan key:generate

# Отредактировать .env
nano .env
```

### Обязательные изменения в .env:

```env
APP_URL=https://your-actual-domain.com
APP_DEBUG=false
APP_ENV=production

DB_HOST=83.69.139.168
DB_DATABASE=uz123_paynes
DB_USERNAME=uz123_SuperAdmin
DB_PASSWORD=Bankir2721

SANCTUM_STATEFUL_DOMAINS=your-actual-domain.com
SESSION_DOMAIN=.your-actual-domain.com
```

## 🗄️ Шаг 5: Миграции и Seeding

```bash
# Запустить миграции
php artisan migrate --force

# Создать роли и права
php artisan db:seed --class=RolesAndPermissionsSeeder

# Создать админа (логин: admin, пароль: admin123)
php artisan db:seed --class=AdminUserSeeder

# Загрузить курсы валют
php artisan db:seed --class=ExchangeRatesSeeder

# ИЛИ запустить все сразу
php artisan db:seed --force
```

## 🔒 Шаг 6: Права доступа

```bash
# Установить правильные права
sudo chown -R www-data:www-data /var/www/html/paynes
sudo chmod -R 755 /var/www/html/paynes
sudo chmod -R 775 /var/www/html/paynes/storage
sudo chmod -R 775 /var/www/html/paynes/bootstrap/cache
```

## 🚀 Шаг 7: Оптимизация

```bash
# Кешировать конфигурацию
php artisan config:cache

# Кешировать роуты
php artisan route:cache

# Кешировать views
php artisan view:cache

# Оптимизировать автозагрузку
php artisan optimize

# Очистить старые логи
php artisan log:clear
```

## 🌐 Шаг 8: Настройка Web-сервера

### Для Apache (с .htaccess)

```bash
# Включить mod_rewrite
sudo a2enmod rewrite

# Настроить VirtualHost
sudo nano /etc/apache2/sites-available/paynes.conf
```

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/html/paynes/public

    <Directory /var/www/html/paynes/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/paynes-error.log
    CustomLog ${APACHE_LOG_DIR}/paynes-access.log combined
</VirtualHost>
```

```bash
# Активировать сайт
sudo a2ensite paynes.conf
sudo systemctl reload apache2
```

### Для Nginx

```bash
sudo nano /etc/nginx/sites-available/paynes
```

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/html/paynes/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Активировать сайт
sudo ln -s /etc/nginx/sites-available/paynes /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔐 Шаг 9: SSL сертификат (HTTPS)

```bash
# Установить Certbot
sudo apt install certbot python3-certbot-apache -y
# ИЛИ для Nginx
sudo apt install certbot python3-certbot-nginx -y

# Получить сертификат
sudo certbot --apache -d your-domain.com -d www.your-domain.com
# ИЛИ для Nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Автообновление сертификата
sudo certbot renew --dry-run
```

## 📝 Шаг 10: Обновление public/.htaccess

```bash
nano public/.htaccess
```

Изменить строку 2:

```apache
# Было:
SetEnv APP_ENV local

# Стало:
SetEnv APP_ENV production
```

## ✅ Шаг 11: Проверка работоспособности

### Тестовые URL:

1. **Health Check**: `https://your-domain.com/api/health`
   - Должен вернуть: `{"status":"ok","services":{"database":"connected","cache":"connected"}}`

2. **Главная страница**: `https://your-domain.com`
   - Должна открыться страница входа

3. **API Login**: `POST https://your-domain.com/api/login`
   ```json
   {
     "username": "admin",
     "password": "admin123"
   }
   ```

### Проверка логов:

```bash
# Проверить логи Laravel
tail -f storage/logs/laravel.log

# Проверить логи веб-сервера
# Apache:
tail -f /var/log/apache2/paynes-error.log

# Nginx:
tail -f /var/log/nginx/error.log
```

## 🔄 Шаг 12: Настройка Cron (опционально)

Для автоматических задач (очистка логов, бэкапы):

```bash
sudo crontab -e
```

Добавить:

```cron
# Laravel Scheduler (если будет использоваться)
* * * * * cd /var/www/html/paynes && php artisan schedule:run >> /dev/null 2>&1

# Ежедневная очистка старых логов (опционально)
0 2 * * * cd /var/www/html/paynes && php artisan log:clear --days=30

# Еженедельный бэкап БД (опционально)
0 3 * * 0 mysqldump -u uz123_SuperAdmin -pBankir2721 uz123_paynes > /backups/paynes_$(date +\%Y\%m\%d).sql
```

## 📊 Шаг 13: Мониторинг и логирование

### Настройка мониторинга ошибок (опционально):

1. **Sentry** (рекомендуется):
```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=your-sentry-dsn
```

2. **Laravel Telescope** (для разработки/отладки):
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

## 🔄 Обновление приложения

Для будущих обновлений:

```bash
cd /var/www/html/paynes

# 1. Включить режим обслуживания
php artisan down

# 2. Получить новый код
git pull origin main
# ИЛИ загрузить новые файлы

# 3. Обновить зависимости
composer install --optimize-autoloader --no-dev
npm install
npm run build

# 4. Мигрировать БД (если есть новые миграции)
php artisan migrate --force

# 5. Очистить и обновить кеши
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. Выключить режим обслуживания
php artisan up
```

## 🛡️ Безопасность

### Чеклист безопасности:

- [ ] `APP_DEBUG=false` в production
- [ ] `APP_ENV=production` в .env и .htaccess
- [ ] SSL сертификат установлен (HTTPS)
- [ ] Firewall настроен (UFW/iptables)
- [ ] Регулярные бэкапы БД
- [ ] Сильные пароли БД
- [ ] Права 755 на папки, 644 на файлы
- [ ] .env файл не доступен извне
- [ ] Логи мониторятся
- [ ] Rate limiting активен

## 📞 Поддержка

При возникновении проблем:

1. Проверить логи: `storage/logs/laravel.log`
2. Проверить права доступа: `ls -la storage bootstrap/cache`
3. Проверить .env конфигурацию
4. Проверить подключение к БД: `php artisan migrate:status`
5. Проверить кеши: `php artisan config:clear && php artisan cache:clear`

## 🎉 Готово!

Приложение развернуто и готово к использованию!

**Данные для входа по умолчанию:**
- Логин: `admin`
- Пароль: `admin123`

⚠️ **ВАЖНО**: Сразу после первого входа смените пароль администратора!
