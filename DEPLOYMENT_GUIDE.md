# 🚀 Руководство по развертыванию на CPanel

## Требования хостинга

- ✅ PHP 8.3 или выше
- ✅ MySQL 5.7 или выше
- ✅ Composer
- ✅ SSH доступ (желательно)
- ✅ Node.js/npm (для сборки, если нет - можно собрать локально)

---

## Шаг 1: Подготовка проекта локально

### 1.1 Соберите frontend

```bash
cd /Users/ilyosxon/Downloads/admin-kassa-laravel

# Установите зависимости (если еще не установлены)
npm install

# Соберите для production
npm run build
```

Это создаст папку `public/build` с оптимизированными файлами.

### 1.2 Настройте .env для production

Создайте `.env.production`:

```env
APP_NAME="Anesi Kassa"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY_ON_SERVER
APP_DEBUG=false
APP_TIMEZONE=Asia/Tashkent
APP_URL=https://ваш-домен.uz

APP_LOCALE=ru
APP_FALLBACK_LOCALE=ru
APP_FAKER_LOCALE=ru_RU

# MySQL база данных (получите от хостинг-провайдера)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ваша_база_данных
DB_USERNAME=ваш_пользователь
DB_PASSWORD=ваш_пароль

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Sanctum
SANCTUM_STATEFUL_DOMAINS=ваш-домен.uz
SESSION_DOMAIN=.ваш-домен.uz

# CORS (если API на отдельном поддомене)
FRONTEND_URL=https://ваш-домен.uz
```

### 1.3 Оптимизируйте autoload

```bash
composer install --optimize-autoloader --no-dev
```

---

## Шаг 2: Создание базы данных в CPanel

### 2.1 Войдите в CPanel

1. Откройте CPanel вашего хостинга
2. Найдите раздел **"Базы данных"** или **"MySQL Databases"**

### 2.2 Создайте базу данных

1. Введите имя базы данных, например: `anesi_kassa`
2. Нажмите **"Создать базу данных"**
3. Запишите полное имя (обычно `username_anesi_kassa`)

### 2.3 Создайте пользователя

1. В разделе **"Пользователи MySQL"**
2. Создайте нового пользователя
3. Установите надежный пароль
4. Запишите имя пользователя и пароль

### 2.4 Назначьте права

1. В разделе **"Добавить пользователя к базе данных"**
2. Выберите созданного пользователя и базу данных
3. Отметьте **"ВСЕ ПРИВИЛЕГИИ"**
4. Сохраните

---

## Шаг 3: Загрузка файлов на сервер

### Вариант A: Через File Manager (проще)

1. В CPanel откройте **"Диспетчер файлов"** (File Manager)
2. Перейдите в `public_html` (или вашу корневую папку)
3. Создайте папку `anesi-kassa` (или любое другое имя)
4. Войдите в эту папку
5. Загрузите архив проекта или файлы напрямую
6. Если загрузили ZIP - распакуйте его

**Что загружать:**
```
/app
/bootstrap
/config
/database
/public
/resources
/routes
/storage
/vendor (если собрали локально)
.env.production (переименуйте в .env)
artisan
composer.json
composer.lock
package.json
vite.config.js
```

**НЕ загружайте:**
```
/node_modules (слишком большая)
/.git
.env (локальный)
```

### Вариант B: Через FTP (FileZilla)

1. Скачайте FileZilla
2. Подключитесь к FTP (данные от хостинг-провайдера)
3. Загрузите все файлы в нужную папку

### Вариант C: Через SSH/Git (если доступен)

```bash
# На сервере
cd ~/public_html
git clone https://github.com/ваш-репозиторий/anesi-kassa.git
cd anesi-kassa

# Или загрузите через rsync
```

---

## Шаг 4: Настройка прав доступа

### 4.1 Через File Manager

В CPanel File Manager:

1. Найдите папки `storage` и `bootstrap/cache`
2. Кликните правой кнопкой → **Permissions** (Права доступа)
3. Установите **755** для папок
4. Установите **644** для файлов
5. Отметьте **"Применить рекурсивно"** (Recursively)

### 4.2 Через SSH (если доступен)

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 644 storage/*.log
```

---

## Шаг 5: Настройка .env на сервере

### 5.1 Скопируйте .env.production в .env

В File Manager:
1. Найдите файл `.env.production`
2. Скопируйте и переименуйте в `.env`
3. Отредактируйте файл (кликните → Edit)

### 5.2 Заполните данные базы данных

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_anesi_kassa  # ваше полное имя БД
DB_USERNAME=username_dbuser       # ваш пользователь БД
DB_PASSWORD=ваш_пароль_от_БД
```

### 5.3 Установите домен

```env
APP_URL=https://ваш-домен.uz
SANCTUM_STATEFUL_DOMAINS=ваш-домен.uz
SESSION_DOMAIN=.ваш-домен.uz
FRONTEND_URL=https://ваш-домен.uz
```

---

## Шаг 6: Установка Composer зависимостей

### Вариант A: Через SSH (рекомендуется)

```bash
cd ~/public_html/anesi-kassa
composer install --optimize-autoloader --no-dev
```

### Вариант B: Если нет SSH

1. Соберите `vendor` локально с `--no-dev`:
```bash
composer install --optimize-autoloader --no-dev
```

2. Загрузите папку `vendor` на сервер через File Manager или FTP

---

## Шаг 7: Генерация APP_KEY

### Через SSH:

```bash
php artisan key:generate
```

### Без SSH:

1. Создайте временный PHP файл `generate-key.php` в корне:

```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('key:generate');

echo "APP_KEY generated! Check your .env file.";
```

2. Откройте в браузере: `https://ваш-домен.uz/generate-key.php`
3. Удалите файл после генерации!

---

## Шаг 8: Запуск миграций

### Через SSH:

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Без SSH - через временный файл:

Создайте `migrate.php`:

```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Запуск миграций
$kernel->call('migrate', ['--force' => true]);
echo "Migrations completed!\n";

// Запуск сидеров
$kernel->call('db:seed', ['--force' => true]);
echo "Seeding completed!\n";
```

Откройте в браузере и удалите после выполнения!

---

## Шаг 9: Настройка веб-сервера

### 9.1 Создайте .htaccess в корне проекта

Создайте файл `.htaccess` в папке `anesi-kassa`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 9.2 Проверьте .htaccess в public

Файл `public/.htaccess` должен содержать:

```apache
<IfModule mod_negotiation.c>
    Options -MultiViews -Indexes
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Disable directory browsing
Options -Indexes

# Hide .env file
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### 9.3 Настройте Document Root в CPanel

В CPanel найдите **"Domains"** или **"Addon Domains"**:

1. Для главного домена - установите Document Root: `public_html/anesi-kassa/public`
2. Для поддомена - создайте поддомен с корнем в `public_html/anesi-kassa/public`

---

## Шаг 10: Оптимизация для production

### Через SSH:

```bash
# Кэширование конфигурации
php artisan config:cache

# Кэширование роутов
php artisan route:cache

# Кэширование views
php artisan view:cache

# Оптимизация
php artisan optimize
```

### Без SSH - через PHP файл:

Создайте `optimize.php`:

```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('config:cache');
$kernel->call('route:cache');
$kernel->call('view:cache');
$kernel->call('optimize');

echo "Optimization completed!";
```

---

## Шаг 11: Настройка HTTPS (SSL)

### В CPanel:

1. Найдите **"SSL/TLS"** или **"Let's Encrypt SSL"**
2. Выберите ваш домен
3. Нажмите **"Установить SSL сертификат"**
4. Дождитесь активации (обычно 5-10 минут)

### Принудительный HTTPS

Добавьте в `.htaccess` (в корне):

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST%{REQUEST_URI} [L,R=301]
```

---

## Шаг 12: Проверка работы

### 12.1 Откройте сайт

Перейдите по адресу: `https://ваш-домен.uz`

Вы должны увидеть страницу входа!

### 12.2 Тестовый вход

- Логин: `admin`
- Пароль: `admin123`

### 12.3 Проверка API

Проверьте: `https://ваш-домен.uz/api/rates/latest`

Должен вернуть JSON с текущим курсом.

---

## Troubleshooting (Решение проблем)

### Ошибка 500

1. Включите debug в `.env`:
```env
APP_DEBUG=true
```

2. Проверьте права на папки:
```bash
chmod -R 755 storage bootstrap/cache
```

3. Проверьте логи: `storage/logs/laravel.log`

### Ошибка "No application encryption key"

```bash
php artisan key:generate
```

### Ошибка базы данных

1. Проверьте данные в `.env`
2. Проверьте права пользователя БД
3. Убедитесь, что MySQL запущен

### Белый экран

1. Проверьте Document Root (должен быть `/public`)
2. Проверьте `.htaccess` файлы
3. Очистите кэш браузера

### API не работает (401/404)

1. Проверьте `.htaccess` в `public/`
2. Убедитесь, что `mod_rewrite` включен
3. Проверьте CORS в `.env`

### После обновления кода

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Потом снова кэшируйте
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Безопасность на production

### ✅ Обязательно:

1. **Установите APP_DEBUG=false**
```env
APP_DEBUG=false
```

2. **Удалите тестовые файлы:**
- `generate-key.php`
- `migrate.php`
- `optimize.php`

3. **Смените пароли пользователей:**
```bash
php artisan tinker
>>> $user = User::where('login', 'admin')->first();
>>> $user->password = bcrypt('новый_сложный_пароль');
>>> $user->save();
```

4. **Настройте CORS** в `config/cors.php`

5. **Проверьте права файлов:**
- Папки: 755
- Файлы: 644
- `.env`: 600

6. **Регулярно обновляйте зависимости:**
```bash
composer update --no-dev
```

---

## Резервное копирование

### Автоматический backup БД

Создайте cron job в CPanel:

```bash
# Каждый день в 3:00
0 3 * * * mysqldump -u username -ppassword database_name > /home/username/backups/db_$(date +\%Y\%m\%d).sql
```

### Backup файлов

```bash
# Каждую неделю
0 0 * * 0 tar -czf /home/username/backups/site_$(date +\%Y\%m\%d).tar.gz /home/username/public_html/anesi-kassa
```

---

## Мониторинг

### Проверка логов

Регулярно проверяйте:
- `storage/logs/laravel.log`
- CPanel → Error Logs

### Производительность

1. Включите opcache (PHP Extensions в CPanel)
2. Используйте CDN для статики
3. Настройте кэширование браузера

---

## Контрольный список перед запуском

- [ ] База данных создана и пользователь назначен
- [ ] Все файлы загружены на сервер
- [ ] `.env` настроен с правильными данными
- [ ] Права на папки установлены (755/644)
- [ ] APP_KEY сгенерирован
- [ ] Composer зависимости установлены
- [ ] Миграции выполнены
- [ ] Сидеры запущены (тестовые пользователи созданы)
- [ ] Document Root указывает на `/public`
- [ ] SSL сертификат установлен
- [ ] HTTPS работает
- [ ] Вход в систему работает
- [ ] API endpoints отвечают
- [ ] Тестовые файлы удалены
- [ ] APP_DEBUG=false
- [ ] Пароли администратора изменены
- [ ] Backup настроен

---

## Готово! 🎉

Ваше приложение Anesi Kassa запущено на production!

**Адрес:** https://ваш-домен.uz

**Тестовый вход (измените пароль!):**
- Логин: admin
- Пароль: admin123

---

## Дополнительные ресурсы

- [Laravel Deployment Documentation](https://laravel.com/docs/11.x/deployment)
- [CPanel Documentation](https://docs.cpanel.net/)
- [Vue.js Production Deployment](https://vuejs.org/guide/best-practices/production-deployment.html)
