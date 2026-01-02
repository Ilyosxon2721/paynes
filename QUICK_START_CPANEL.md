# Quick Start: Развертывание на cPanel (15 минут)

## Шаг 1: Создайте базу данных (3 мин)

1. Войдите в cPanel
2. MySQL® Databases → Create New Database
   - Name: `admin_kassa`
3. Create New User
   - Username: `admin_kassa_user`
   - Password: (сгенерируйте сложный пароль)
4. Add User To Database
   - ALL PRIVILEGES

**Запишите:** DB_DATABASE, DB_USERNAME, DB_PASSWORD

## Шаг 2: Загрузите файлы (5 мин)

### Вариант A: Git Clone (быстрее)
```bash
cd ~/public_html
git clone https://github.com/Ilyosxon2721/admin_kassa.git
cd admin_kassa
```

### Вариант B: File Manager
1. Загрузите ZIP
2. Extract в public_html/admin_kassa

## Шаг 3: Настройте .env (2 мин)

```bash
cd ~/public_html/admin_kassa
cp .env.production .env
nano .env
```

Обновите:
```env
APP_URL=https://yourdomain.com
DB_DATABASE=yourusername_admin_kassa
DB_USERNAME=yourusername_admin_kassa_user
DB_PASSWORD=your_password
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

## Шаг 4: Установка (3 мин)

```bash
# Composer
composer install --no-dev --optimize-autoloader

# Генерация ключа
php artisan key:generate

# Миграции
php artisan migrate --force

# Кэш
php artisan config:cache
php artisan route:cache
php artisan storage:link

# Права
chmod -R 775 storage bootstrap/cache
```

## Шаг 5: Document Root (1 мин)

cPanel → Domains → yourdomain.com
- Document Root: `/home/username/public_html/admin_kassa/public`

## Шаг 6: Создайте админа (1 мин)

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'login' => 'admin',
    'full_name' => 'Administrator',
    'password' => bcrypt('admin123'),
    'position' => 'admin',
    'status' => 'active'
]);
exit
```

## ✅ Готово!

Откройте: https://yourdomain.com

Логин: admin
Пароль: admin123

---

**Проблемы?** См. [CPANEL_DEPLOYMENT.md](CPANEL_DEPLOYMENT.md)
