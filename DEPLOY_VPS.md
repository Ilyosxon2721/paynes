# 🚀 Деплой Laravel проекта на VPS (Ubuntu 22.04/24.04)

## Содержание

1. [Подготовка сервера](#1-подготовка-сервера)
2. [Установка зависимостей](#2-установка-зависимостей)
3. [Настройка базы данных](#3-настройка-базы-данных)
4. [Клонирование проекта](#4-клонирование-проекта)
5. [Настройка Nginx](#5-настройка-nginx)
6. [SSL сертификат](#6-ssl-сертификат)
7. [Supervisor для очередей](#7-supervisor-для-очередей)
8. [Firewall и безопасность](#8-firewall-и-безопасность)
9. [Автоматическое обновление](#9-автоматическое-обновление)

---

## 1. Подготовка сервера

### Подключение к серверу

```bash
ssh root@ваш_ip_адрес
```

### Обновление системы

```bash
apt update && apt upgrade -y
```

### Установка базовых пакетов

```bash
apt install -y software-properties-common curl wget git unzip
```

### Создание пользователя для деплоя (рекомендуется)

```bash
# Создаем пользователя
adduser deploy
# Пароль: создайте надежный пароль

# Добавляем в sudo группу
usermod -aG sudo deploy

# Переключаемся на нового пользователя
su - deploy
```

---

## 2. Установка зависимостей

### 2.1 Установка PHP 8.3

```bash
# Добавляем репозиторий PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Устанавливаем PHP 8.3 и расширения
sudo apt install -y php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring \
    php8.3-curl php8.3-xml php8.3-bcmath php8.3-intl \
    php8.3-redis php8.3-opcache

# Проверяем версию
php -v
```

### 2.2 Установка Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Проверяем
composer --version
```

### 2.3 Установка Node.js и npm

```bash
# Устанавливаем Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Проверяем
node --version
npm --version
```

### 2.4 Установка Nginx

```bash
sudo apt install -y nginx

# Запускаем и добавляем в автозагрузку
sudo systemctl start nginx
sudo systemctl enable nginx

# Проверяем статус
sudo systemctl status nginx
```

### 2.5 Установка MySQL

```bash
sudo apt install -y mysql-server

# Запускаем
sudo systemctl start mysql
sudo systemctl enable mysql

# Настраиваем безопасность
sudo mysql_secure_installation
```

**Ответы на вопросы:**
- Validate Password Plugin: `Y` (да)
- Password strength level: `2` (сильный)
- Change root password: `Y` → введите надежный пароль
- Remove anonymous users: `Y`
- Disallow root login remotely: `Y`
- Remove test database: `Y`
- Reload privilege tables: `Y`

---

## 3. Настройка базы данных

### Создание базы данных и пользователя

```bash
# Заходим в MySQL
sudo mysql -u root -p

# В консоли MySQL:
```

```sql
-- Создаем базу данных
CREATE DATABASE admin_kassa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Создаем пользователя
CREATE USER 'paynes_user'@'localhost' IDENTIFIED BY 'ваш_надежный_пароль';

-- Даем права
GRANT ALL PRIVILEGES ON admin_kassa.* TO 'paynes_user'@'localhost';

-- Применяем изменения
FLUSH PRIVILEGES;

-- Выходим
EXIT;
```

---

## 4. Клонирование проекта

### 4.1 Настройка SSH ключа для GitHub

```bash
# Генерируем SSH ключ
ssh-keygen -t ed25519 -C "your_email@example.com"
# Нажмите Enter несколько раз (без пароля для автоматизации)

# Показываем публичный ключ
cat ~/.ssh/id_ed25519.pub
```

**Скопируйте вывод и добавьте в GitHub:**
1. GitHub → Settings → SSH and GPG keys → New SSH key
2. Вставьте ключ и сохраните

### 4.2 Клонирование репозитория

```bash
# Переходим в домашнюю директорию
cd ~

# Клонируем репозиторий
git clone git@github.com:Ilyosxon2721/admin_kassa.git

# Переходим в проект
cd admin_kassa
```

### 4.3 Установка зависимостей

```bash
# Composer зависимости
composer install --no-dev --optimize-autoloader

# NPM зависимости и сборка
npm ci --production
npm run build
```

### 4.4 Настройка .env

```bash
# Копируем .env
cp .env.production.example .env

# Редактируем .env
nano .env
```

**Важные настройки в .env:**

```ini
APP_NAME="Paynes"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin_kassa
DB_USERNAME=paynes_user
DB_PASSWORD=ваш_пароль_из_mysql

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Сохраните: `Ctrl+O`, `Enter`, `Ctrl+X`

### 4.5 Генерация ключа и миграции

```bash
# Генерируем APP_KEY
php artisan key:generate

# Запускаем миграции
php artisan migrate --force

# (Опционально) Seeding
php artisan db:seed --force

# Настраиваем права
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4.6 Оптимизация

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 5. Настройка Nginx

### 5.1 Создание конфигурации сайта

```bash
sudo nano /etc/nginx/sites-available/admin_kassa
```

**Вставьте следующую конфигурацию:**

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;

    root /home/deploy/admin_kassa/public;
    index index.php index.html;

    # Логи
    access_log /var/log/nginx/admin_kassa_access.log;
    error_log /var/log/nginx/admin_kassa_error.log;

    # Увеличиваем лимиты
    client_max_body_size 100M;

    # Основная локация
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Запрет доступа к скрытым файлам
    location ~ /\. {
        deny all;
    }

    # Кеширование статики
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Сохраните: `Ctrl+O`, `Enter`, `Ctrl+X`

### 5.2 Активация сайта

```bash
# Создаем симлинк
sudo ln -s /etc/nginx/sites-available/admin_kassa /etc/nginx/sites-enabled/

# Удаляем дефолтный сайт
sudo rm /etc/nginx/sites-enabled/default

# Проверяем конфигурацию
sudo nginx -t

# Перезапускаем Nginx
sudo systemctl reload nginx
```

---

## 6. SSL сертификат (Let's Encrypt)

### 6.1 Установка Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 6.2 Получение сертификата

```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

**Ответы на вопросы:**
- Email: ваш email
- Terms of Service: `Y`
- Share email: `N`
- Redirect HTTP to HTTPS: `2` (да, редирект)

### 6.3 Автоматическое обновление сертификата

```bash
# Проверяем автообновление
sudo certbot renew --dry-run

# Cron уже настроен автоматически
```

---

## 7. Supervisor для очередей

### 7.1 Установка Supervisor

```bash
sudo apt install -y supervisor
```

### 7.2 Создание конфигурации для Laravel Queue

```bash
sudo nano /etc/supervisor/conf.d/admin_kassa_worker.conf
```

**Вставьте:**

```ini
[program:admin_kassa_worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/deploy/admin_kassa/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=/home/deploy/admin_kassa/storage/logs/worker.log
stopwaitsecs=3600
```

Сохраните: `Ctrl+O`, `Enter`, `Ctrl+X`

### 7.3 Запуск Supervisor

```bash
# Перечитываем конфиги
sudo supervisorctl reread

# Обновляем
sudo supervisorctl update

# Запускаем
sudo supervisorctl start admin_kassa_worker:*

# Проверяем статус
sudo supervisorctl status
```

---

## 8. Firewall и безопасность

### 8.1 Настройка UFW (Firewall)

```bash
# Разрешаем SSH (ВАЖНО! Сделайте это первым!)
sudo ufw allow 22/tcp

# Разрешаем HTTP и HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Включаем firewall
sudo ufw enable

# Проверяем статус
sudo ufw status
```

### 8.2 Установка Redis

```bash
sudo apt install -y redis-server

# Настраиваем
sudo nano /etc/redis/redis.conf
```

Найдите и измените:
```
supervised no → supervised systemd
```

Сохраните и перезапустите:

```bash
sudo systemctl restart redis
sudo systemctl enable redis

# Проверяем
redis-cli ping
# Ответ: PONG
```

### 8.3 Настройка PHP для production

```bash
sudo nano /etc/php/8.3/fpm/php.ini
```

Найдите и измените:
```ini
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 512M
max_execution_time = 300
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

Перезапустите PHP-FPM:
```bash
sudo systemctl restart php8.3-fpm
```

---

## 9. Автоматическое обновление

### 9.1 Создание скрипта деплоя

```bash
nano ~/admin_kassa/deploy-production.sh
```

**Вставьте:**

```bash
#!/bin/bash

cd /home/deploy/admin_kassa

# Включаем режим обслуживания
php artisan down

# Pull изменений
git pull origin main

# Обновляем зависимости
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# Миграции
php artisan migrate --force

# Очищаем кеши
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Создаем новые кеши
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Перезапускаем воркеры
sudo supervisorctl restart admin_kassa_worker:*

# Выключаем режим обслуживания
php artisan up

echo "✅ Deployment completed!"
```

Сделайте исполняемым:
```bash
chmod +x ~/admin_kassa/deploy-production.sh
```

### 9.2 Настройка Laravel Scheduler

```bash
crontab -e
```

Добавьте строку:
```
* * * * * cd /home/deploy/admin_kassa && php artisan schedule:run >> /dev/null 2>&1
```

---

## 10. Проверка деплоя

### 10.1 Проверка сервисов

```bash
# Nginx
sudo systemctl status nginx

# PHP-FPM
sudo systemctl status php8.3-fpm

# MySQL
sudo systemctl status mysql

# Redis
sudo systemctl status redis

# Supervisor
sudo supervisorctl status
```

### 10.2 Проверка логов

```bash
# Логи Nginx
sudo tail -f /var/log/nginx/admin_kassa_error.log

# Логи Laravel
tail -f ~/admin_kassa/storage/logs/laravel.log

# Логи воркеров
tail -f ~/admin_kassa/storage/logs/worker.log
```

### 10.3 Проверка в браузере

Откройте: `https://yourdomain.com`

---

## 11. Мониторинг и обслуживание

### Полезные команды

```bash
# Просмотр процессов PHP
ps aux | grep php

# Использование памяти
free -h

# Использование диска
df -h

# Перезапуск всех сервисов
sudo systemctl restart nginx php8.3-fpm mysql redis

# Очистка логов (если много места занято)
sudo truncate -s 0 /var/log/nginx/*.log
php artisan log:clear
```

---

## 12. Чеклист готовности к production

- [ ] Сервер обновлен и защищен
- [ ] Установлены все зависимости (PHP 8.3, Nginx, MySQL, Redis, Node.js)
- [ ] База данных создана и настроена
- [ ] Проект склонирован и настроен
- [ ] .env правильно настроен (production настройки)
- [ ] APP_KEY сгенерирован
- [ ] Миграции выполнены
- [ ] Nginx настроен и работает
- [ ] SSL сертификат установлен (HTTPS работает)
- [ ] Firewall настроен (UFW)
- [ ] Redis установлен и работает
- [ ] Supervisor настроен для очередей
- [ ] Cron настроен для Laravel Scheduler
- [ ] Права доступа настроены (775 storage/bootstrap)
- [ ] Кеши созданы (config, route, view)
- [ ] Логи проверены на ошибки
- [ ] Сайт открывается в браузере
- [ ] Скрипт деплоя создан и протестирован

---

## 🎉 Готово!

Ваше приложение теперь работает на production сервере с:
- ✅ HTTPS (SSL)
- ✅ Firewall
- ✅ Queue workers
- ✅ Redis кеш
- ✅ Автоматический деплой

**Для обновления проекта:**
```bash
cd ~/admin_kassa
./deploy-production.sh
```

---

## Поддержка и помощь

**Документация:**
- Laravel: https://laravel.com/docs
- Nginx: https://nginx.org/ru/docs/
- Certbot: https://certbot.eff.org/

**Полезные команды для диагностики:**
```bash
# Проверка PHP
php -v
php -m

# Проверка портов
sudo netstat -tlnp

# Проверка дискового пространства
du -sh ~/admin_kassa/*
```
