# ⚡ Быстрый деплой на CPanel

## За 10 минут на хостинг!

### Шаг 1: Подготовка (локально)

```bash
cd /Users/ilyosxon/Downloads/admin-kassa-laravel

# Соберите проект
npm run build

# Установите production зависимости
composer install --optimize-autoloader --no-dev
```

---

### Шаг 2: Создайте БД в CPanel

1. Войдите в CPanel
2. **MySQL Databases** → Создать базу данных
3. Создать пользователя БД
4. Назначить ВСЕ ПРАВА пользователю

**Запишите:**
- Имя БД: `username_anesi_kassa`
- Пользователь: `username_dbuser`
- Пароль: `ваш_пароль`

---

### Шаг 3: Загрузите файлы

**Что загружать:**

✅ Обязательно:
```
/app
/bootstrap
/config
/database
/public (с собранным build/)
/resources
/routes
/storage
/vendor
.env.production
.htaccess
artisan
composer.json
composer.lock
```

❌ НЕ загружайте:
```
/node_modules
/.git
.env (локальный)
```

**Куда загружать:**
- В `public_html/` или
- В `public_html/anesi-kassa/`

---

### Шаг 4: Настройте .env

1. Переименуйте `.env.production` в `.env`
2. Отредактируйте файл:

```env
# Данные БД
DB_DATABASE=username_anesi_kassa
DB_USERNAME=username_dbuser
DB_PASSWORD=ваш_пароль_от_БД

# Ваш домен
APP_URL=https://ваш-домен.uz
SANCTUM_STATEFUL_DOMAINS=ваш-домен.uz
SESSION_DOMAIN=.ваш-домен.uz
```

---

### Шаг 5: Установите права

В File Manager:

**Папки → 755:**
- `storage/` (рекурсивно)
- `bootstrap/cache/` (рекурсивно)

**Файл .env → 600**

---

### Шаг 6: Сгенерируйте APP_KEY

**Через SSH:**
```bash
php artisan key:generate
```

**Без SSH:** Создайте файл `key.php` в корне:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('key:generate');
echo "✅ APP_KEY сгенерирован!";
```

Откройте `https://ваш-домен.uz/key.php`, потом **удалите файл!**

---

### Шаг 7: Запустите миграции

**Через SSH:**
```bash
php artisan migrate --force
php artisan db:seed --force
```

**Без SSH:** Создайте файл `setup.php`:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Запуск миграций...\n";
$kernel->call('migrate', ['--force' => true]);

echo "Запуск сидеров...\n";
$kernel->call('db:seed', ['--force' => true]);

echo "✅ База данных готова!";
```

Откройте `https://ваш-домен.uz/setup.php`, потом **удалите файл!**

---

### Шаг 8: Настройте Document Root

В CPanel → **Domains**:

Установите Document Root: `public_html/anesi-kassa/public`

(или `public_html/public` если загрузили в корень)

---

### Шаг 9: Включите SSL

CPanel → **SSL/TLS** → Let's Encrypt → Установить

Дождитесь активации (5-10 минут)

---

### Шаг 10: Оптимизация

**Через SSH:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Без SSH:** Создайте `optimize.php`:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('config:cache');
$kernel->call('route:cache');
$kernel->call('view:cache');
$kernel->call('optimize');

echo "✅ Оптимизация завершена!";
```

Откройте и **удалите!**

---

## ✅ Проверка

Откройте: `https://ваш-домен.uz`

**Вход:**
- Логин: `admin`
- Пароль: `admin123`

---

## 🔒 Безопасность (обязательно!)

1. **Установите APP_DEBUG=false** в `.env`
2. **Удалите все .php файлы** из корня (key.php, setup.php, optimize.php)
3. **Смените пароль администратора** после входа
4. **Проверьте права файлов** (.env должен быть 600)

---

## 🆘 Если что-то не работает

### Ошибка 500
- Проверьте `.env` (данные БД)
- Проверьте права на `storage/`
- Включите `APP_DEBUG=true` временно

### Белый экран
- Проверьте Document Root (должен быть `/public`)
- Проверьте `.htaccess` в public/

### API не работает (401)
- Очистите кэш: `php artisan config:clear`
- Проверьте SANCTUM_STATEFUL_DOMAINS в `.env`

### После обновления
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

---

## 📞 Готово!

Ваш сайт работает: `https://ваш-домен.uz`

**Полная документация:** [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)
