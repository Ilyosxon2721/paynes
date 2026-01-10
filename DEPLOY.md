# 🚀 Инструкция по деплою на cPanel из GitHub

## Предварительные требования

- Доступ к cPanel
- SSH доступ (опционально, но рекомендуется)
- GitHub репозиторий: `Ilyosxon2721/admin_kassa`
- PHP 8.3 или выше
- MySQL/MariaDB база данных
- Composer
- Node.js и npm

---

## Метод 1: Деплой через cPanel Git™ Version Control (Рекомендуется)

### Шаг 1: Подключение GitHub в cPanel

1. **Войдите в cPanel**
2. **Найдите "Git™ Version Control"** в разделе "Files"
3. **Нажмите "Create"**

### Шаг 2: Клонирование репозитория

Заполните форму:

```
Repository URL: https://github.com/Ilyosxon2721/admin_kassa.git
или
git@github.com:Ilyosxon2721/admin_kassa.git (если настроен SSH ключ)

Repository Path: /home/вашuser/repositories/admin_kassa
(Или любой путь за пределами public_html)

Repository Name: admin_kassa (опционально)
```

4. **Нажмите "Create"**

### Шаг 3: Настройка SSH ключа (если используете git@)

Если вы используете SSH URL, нужно добавить публичный ключ в GitHub:

1. В cPanel → **SSH Access** → **Manage SSH Keys**
2. Сгенерируйте новый ключ или используйте существующий
3. Скопируйте публичный ключ
4. Перейдите в GitHub → **Settings** → **Deploy keys** → **Add deploy key**
5. Вставьте ключ и дайте доступ на чтение

### Шаг 4: Pull изменений

1. В **Git™ Version Control** найдите ваш репозиторий
2. Нажмите **Manage**
3. Выберите ветку **main** (или нужную вам)
4. Нажмите **Pull or Deploy** → **Update from Remote**

---

## Шаг 5: Настройка через SSH (Terminal)

**Подключитесь к серверу через SSH:**

```bash
ssh вашuser@ваш-домен.com
```

**Перейдите в директорию проекта:**

```bash
cd ~/repositories/admin_kassa
# или
cd /home/вашuser/repositories/admin_kassa
```

### 5.1 Установка Composer зависимостей

```bash
# Если composer не установлен глобально, скачайте его:
wget https://getcomposer.org/download/latest-stable/composer.phar
chmod +x composer.phar

# Установка зависимостей
php composer.phar install --no-dev --optimize-autoloader
```

### 5.2 Создание .env файла

```bash
# Скопируйте .env.example
cp .env.example .env

# Отредактируйте .env
nano .env
# или
vi .env
```

**Важные настройки в .env:**

```ini
APP_NAME="Paynes"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ваш-домен.com

# База данных (получите из cPanel → MySQL Databases)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ваша_база
DB_USERNAME=ваш_пользователь
DB_PASSWORD=ваш_пароль

# Настройки сессий для production
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Настройки кеша (если Redis доступен)
CACHE_STORE=database
QUEUE_CONNECTION=database

# Отключите broadcasting (т.к. удалили WebSocket)
BROADCAST_CONNECTION=null
```

### 5.3 Генерация ключа приложения

```bash
php artisan key:generate
```

### 5.4 Установка npm зависимостей и сборка

```bash
# Установка зависимостей
npm ci --production

# Сборка фронтенда
npm run build
```

### 5.5 Миграции базы данных

```bash
# Запуск миграций
php artisan migrate --force

# (Опционально) Заполнение начальными данными
php artisan db:seed --force
```

### 5.6 Настройка прав доступа

```bash
# Права на storage и bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Если нужно, измените владельца на пользователя веб-сервера
# (узнайте у хостинг-провайдера)
# chown -R вашuser:вашuser storage bootstrap/cache
```

### 5.7 Оптимизация для production

```bash
# Очистка старых кешей
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Создание новых кешей
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## Шаг 6: Настройка public_html

Laravel требует, чтобы корневой директорией был **public/** внутри проекта.

### Вариант A: Симлинк (Рекомендуется)

```bash
# Удалите/переименуйте старый public_html
mv ~/public_html ~/public_html.backup

# Создайте симлинк на папку public
ln -s ~/repositories/admin_kassa/public ~/public_html
```

### Вариант B: Изменение Document Root в cPanel

1. cPanel → **Domains** → **Domains** или **Addon Domains**
2. Найдите ваш домен
3. Нажмите **Manage**
4. В поле **Document Root** укажите:
   ```
   /home/вашuser/repositories/admin_kassa/public
   ```
5. Сохраните изменения

### Вариант C: Копирование файлов (Не рекомендуется)

```bash
# Скопируйте содержимое public в public_html
cp -r ~/repositories/admin_kassa/public/* ~/public_html/

# Обновите index.php
nano ~/public_html/index.php
```

В `index.php` измените пути:
```php
require __DIR__.'/../repositories/admin_kassa/vendor/autoload.php';
$app = require_once __DIR__.'/../repositories/admin_kassa/bootstrap/app.php';
```

---

## Шаг 7: Настройка .htaccess

Убедитесь, что в `public/.htaccess` правильные настройки:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirect to HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Laravel routes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## Шаг 8: Создание базы данных через cPanel

1. **cPanel → MySQL® Databases**
2. **Create New Database**
   - Имя: `admin_kassa` (или любое другое)
3. **Create New User**
   - Username: выберите имя
   - Password: сгенерируйте надежный пароль
4. **Add User to Database**
   - Выберите пользователя и базу
   - Дайте **ALL PRIVILEGES**

**Обновите .env файл с этими данными!**

---

## Шаг 9: Настройка Cron Jobs (для Laravel Scheduler)

1. **cPanel → Cron Jobs**
2. Добавьте новую задачу:

```bash
* * * * * cd /home/вашuser/repositories/admin_kassa && php artisan schedule:run >> /dev/null 2>&1
```

---

## Шаг 10: Настройка Queue Worker (опционально)

Если используете очереди:

1. **Через Supervisor** (если доступен):

```ini
[program:admin-kassa-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/вашuser/repositories/admin_kassa/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=вашuser
numprocs=1
redirect_stderr=true
stdout_logfile=/home/вашuser/repositories/admin_kassa/storage/logs/worker.log
```

2. **Через Cron** (альтернатива):

```bash
*/5 * * * * cd /home/вашuser/repositories/admin_kassa && php artisan queue:work --stop-when-empty
```

---

## Использование автоматического скрипта деплоя

В проекте есть готовый скрипт `deploy.sh`:

```bash
cd ~/repositories/admin_kassa
chmod +x deploy.sh
./deploy.sh
```

Скрипт автоматически:
- Установит зависимости (Composer + npm)
- Соберет фронтенд
- Выполнит миграции (с подтверждением)
- Очистит и создаст кеши
- Установит права доступа

---

## Обновление проекта (Git Pull)

### Через cPanel Git™ Version Control:

1. Откройте **Git™ Version Control**
2. Найдите репозиторий → **Manage**
3. Нажмите **Pull or Deploy** → **Update from Remote**

### Через SSH:

```bash
cd ~/repositories/admin_kassa

# Pull последних изменений
git pull origin main

# Запустите деплой скрипт
./deploy.sh
```

---

## Проверка после деплоя

1. **Откройте сайт в браузере:**
   ```
   https://ваш-домен.com
   ```

2. **Проверьте логи на ошибки:**
   ```bash
   tail -f ~/repositories/admin_kassa/storage/logs/laravel.log
   ```

3. **Проверьте версию Laravel:**
   ```bash
   cd ~/repositories/admin_kassa
   php artisan --version
   ```

4. **Проверьте подключение к БД:**
   ```bash
   php artisan migrate:status
   ```

---

## Частые проблемы и решения

### 1. Ошибка 500 Internal Server Error

**Причины:**
- Нет прав на storage/bootstrap/cache
- Неправильный .env
- Не установлен APP_KEY

**Решение:**
```bash
chmod -R 775 storage bootstrap/cache
php artisan key:generate
php artisan config:clear
```

### 2. Белый экран / пустая страница

**Причины:**
- Не собран фронтенд
- Неправильные пути в index.php

**Решение:**
```bash
npm run build
php artisan optimize:clear
```

### 3. 404 на всех страницах кроме главной

**Причины:**
- mod_rewrite не включен
- .htaccess не работает

**Решение:**
- Убедитесь что .htaccess в корне public/
- Проверьте что mod_rewrite включен (обратитесь к хостеру)

### 4. npm install падает с ошибкой памяти

**Решение:**
```bash
# Увеличьте лимит памяти
export NODE_OPTIONS="--max-old-space-size=2048"
npm install
```

### 5. Composer требует расширения PHP

**Решение:**
- Включите нужные расширения в cPanel → **MultiPHP INI Editor**
- Или обратитесь в поддержку хостинга

---

## Рекомендации безопасности

1. ✅ **APP_DEBUG=false** в production
2. ✅ **APP_ENV=production** в .env
3. ✅ Используйте сильные пароли для БД
4. ✅ Настройте SSL сертификат (Let's Encrypt в cPanel)
5. ✅ Регулярно обновляйте зависимости
6. ✅ Включите файрвол (если доступен)
7. ✅ Настройте бэкапы БД (cPanel → Backup)

---

## Поддержка

Если возникли проблемы:

1. Проверьте логи: `storage/logs/laravel.log`
2. Проверьте логи веб-сервера (cPanel → Errors)
3. Проверьте права доступа к файлам
4. Убедитесь что все зависимости установлены

---

## Checklist деплоя

- [ ] Клонирован репозиторий через Git™ Version Control
- [ ] Установлены Composer зависимости
- [ ] Создан и настроен .env файл
- [ ] Сгенерирован APP_KEY
- [ ] Создана база данных в cPanel
- [ ] Установлены npm зависимости
- [ ] Собран фронтенд (npm run build)
- [ ] Выполнены миграции
- [ ] Настроены права доступа (chmod 775)
- [ ] Настроен Document Root на /public
- [ ] Очищены и созданы кеши
- [ ] Настроен Cron для scheduler
- [ ] Проверен сайт в браузере
- [ ] Проверены логи на ошибки

---

**Готово! Ваше приложение развернуто на cPanel! 🎉**
