# 📘 Руководство по миграции Anesi Kassa на Laravel 11

## 🎯 Обзор проекта

Этот проект представляет собой полную миграцию кассовой системы **"Anesi Kassa"** с процедурного PHP на современный Laravel 11 с PHP 8.3, Vue.js 3 SPA и мобильным API.

---

## ✅ Что уже сделано

### 1. Инфраструктура проекта
- ✅ Создан новый Laravel 11 проект
- ✅ Установлены основные пакеты:
  - Laravel Sanctum (аутентификация API)
  - Spatie Laravel Permission (роли и права)
  - Kitloong Laravel Migrations Generator (генератор миграций)

### 2. База данных
- ✅ Созданы все необходимые миграции:
  - `users` - пользователи системы (модифицирована стандартная)
  - `payment_types` - справочник типов платежей
  - `payments` - платежи
  - `exchanges` - обмен валют
  - `credits` - кредиты
  - `incashes` - инкассация
  - `rates` - курсы валют
  - `permission_tables` - таблицы Spatie Permission
  - `personal_access_tokens` - токены Sanctum

### 3. Eloquent модели
- ✅ Созданы все модели с полным функционалом:
  - `User` - с трейтами HasApiTokens, HasRoles
  - `PaymentType` - с методом calculateCommission()
  - `Payment` - с SoftDeletes и скоупами
  - `Exchange` - с скоупами для фильтрации
  - `Credit` - с генератором номеров счетов
  - `Incash` - для операций инкассации
  - `Rate` - с методом getLatest()

---

## 📊 Структура базы данных

### Таблица `users`
```sql
- id (bigint, primary key)
- login (string, unique)
- full_name (string)
- password (string, hashed)
- position (enum: 'admin', 'cashier')
- status (enum: 'active', 'blocked')
- branch (string, nullable)
- salary_percentage (decimal 5,2)
- remember_token (string, nullable)
- created_at, updated_at (timestamps)
```

### Таблица `payment_types`
```sql
- id (bigint, primary key)
- name (string) - название типа
- organization (string, nullable)
- bank (string, nullable)
- account_number (string, nullable)
- mfo (string, nullable)
- inn (string, nullable)
- treasury_account (string, nullable)
- type (string, nullable)
- commission_percentage (decimal 5,2)
- commission_fixed (decimal 15,2)
- created_at, updated_at (timestamps)
```

### Таблица `payments`
```sql
- id (bigint, primary key)
- list_number (string, nullable)
- random_number (string, nullable)
- date (date)
- time (time)
- payment_type_id (bigint, foreign key → payment_types.id, cascade)
- payer_name (string)
- purpose (text, nullable)
- amount (decimal 15,2)
- commission (decimal 15,2)
- total (decimal 15,2)
- payment_method (enum: 'cash', 'card')
- currency (enum: 'UZS', 'USD')
- status (enum: 'pending', 'confirmed', 'deleted', 'processed')
- cashier_id (bigint, foreign key → users.id, cascade)
- created_at, updated_at (timestamps)
- deleted_at (timestamp, nullable) - soft delete
```

### Таблица `exchanges`
```sql
- id (bigint, primary key)
- date (date)
- time (time)
- usd_amount (decimal 15,2)
- uzs_amount (decimal 15,2)
- type (enum: 'buy', 'sell')
- rate (decimal 10,2)
- cashier_id (bigint, foreign key → users.id, cascade)
- created_at, updated_at (timestamps)
- deleted_at (timestamp, nullable) - soft delete
```

### Таблица `credits`
```sql
- id (bigint, primary key)
- date (date)
- time (time)
- recipient (string)
- account_number (string, nullable)
- branch (string, nullable)
- debit (decimal 15,2)
- credit (decimal 15,2)
- confirmed_by (string, nullable)
- status (enum: 'pending', 'confirmed', 'deleted')
- cashier_id (bigint, foreign key → users.id, cascade)
- created_at, updated_at (timestamps)
- deleted_at (timestamp, nullable) - soft delete
```

### Таблица `incashes`
```sql
- id (bigint, primary key)
- date (date)
- time (time)
- account_number (enum: '001', '002', '003', '840')
- amount (decimal 15,2)
- type (string) - расход/приход
- cashier_id (bigint, foreign key → users.id, cascade)
- status (enum: 'pending', 'confirmed', 'deleted')
- created_at, updated_at (timestamps)
- deleted_at (timestamp, nullable) - soft delete
```

### Таблица `rates`
```sql
- id (bigint, primary key)
- buy_rate (decimal 10,2)
- sell_rate (decimal 10,2)
- date (date)
- created_at, updated_at (timestamps)
```

---

## 🔐 Система аутентификации

### Миграция с MD5 на Bcrypt
Старая система использовала:
```php
$pass = md5($pass . "bankir");
```

Новая система использует:
```php
$user->password = bcrypt($password); // или Hash::make($password)
```

### Миграция паролей пользователей
При переносе данных из старой БД необходимо:
1. Попросить пользователей сбросить пароли ИЛИ
2. Создать временную систему конвертации при первом входе

**Пример конвертации:**
```php
// В AuthController
if (md5($password . 'bankir') === $user->old_password_hash) {
    // Старый пароль валиден
    $user->password = bcrypt($password);
    $user->old_password_hash = null;
    $user->save();
}
```

---

## 🔑 Роли и права доступа

### Роли
- **admin** - Супер Админ (полный доступ)
- **cashier** - Пользовательский (ограниченный доступ)

### Права (Permissions)
```php
// Платежи
'payments.create', 'payments.view', 'payments.update', 'payments.delete', 'payments.confirm'

// Обмен валют
'exchanges.create', 'exchanges.view', 'exchanges.delete'

// Кредиты
'credits.create', 'credits.view', 'credits.update', 'credits.delete', 'credits.confirm'

// Инкассация
'incashes.create', 'incashes.view', 'incashes.delete'

// Курсы валют
'rates.create', 'rates.view', 'rates.update'

// Пользователи
'users.view', 'users.create', 'users.update', 'users.delete', 'users.block'

// Отчеты
'reports.view-all', 'reports.view-own', 'reports.export'
```

---

## 📡 API Endpoints (планируется)

### Аутентификация
```
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/refresh
GET    /api/auth/user
```

### Платежи
```
GET    /api/payments
POST   /api/payments
GET    /api/payments/{id}
PUT    /api/payments/{id}
DELETE /api/payments/{id}
POST   /api/payments/{id}/confirm
GET    /api/payments/duplicate/{id}
```

### Типы платежей
```
GET    /api/payment-types
POST   /api/payment-types
GET    /api/payment-types/{id}
PUT    /api/payment-types/{id}
DELETE /api/payment-types/{id}
```

### Обмен валют
```
GET    /api/exchanges
POST   /api/exchanges
GET    /api/exchanges/{id}
DELETE /api/exchanges/{id}
GET    /api/exchanges/buy
GET    /api/exchanges/sell
```

### Кредиты
```
GET    /api/credits
POST   /api/credits
GET    /api/credits/{id}
PUT    /api/credits/{id}
DELETE /api/credits/{id}
POST   /api/credits/{id}/confirm
POST   /api/credits/{id}/repay
```

### Инкассация
```
GET    /api/incashes
POST   /api/incashes
GET    /api/incashes/{id}
DELETE /api/incashes/{id}
```

### Курсы валют
```
GET    /api/rates
POST   /api/rates
GET    /api/rates/latest
```

### Отчеты
```
GET    /api/reports/daily
GET    /api/reports/cashier/{id}
GET    /api/reports/salary/{id}
GET    /api/reports/export/excel
GET    /api/reports/export/pdf
```

---

## 🚀 Следующие шаги

### 1. Установка и настройка системы авторизации
```bash
# Опубликовать конфигурацию Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Запустить миграции
php artisan migrate

# Создать роли и права
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 2. Создание API контроллеров
```bash
# Создать контроллеры
php artisan make:controller API/AuthController
php artisan make:controller API/PaymentController --api
php artisan make:controller API/PaymentTypeController --api
php artisan make:controller API/ExchangeController --api
php artisan make:controller API/CreditController --api
php artisan make:controller API/IncashController --api
php artisan make:controller API/RateController --api
php artisan make:controller API/ReportController
```

### 3. Создание Form Requests для валидации
```bash
php artisan make:request StorePaymentRequest
php artisan make:request UpdatePaymentRequest
php artisan make:request StoreExchangeRequest
php artisan make:request StoreCreditRequest
# и т.д.
```

### 4. Создание API Resources
```bash
php artisan make:resource PaymentResource
php artisan make:resource PaymentCollection
php artisan make:resource ExchangeResource
php artisan make:resource CreditResource
# и т.д.
```

### 5. Настройка Vue.js 3 SPA
```bash
# Установить Vue.js и зависимости
npm install vue@next vue-router@4 pinia axios

# Установить Vite плагины
npm install @vitejs/plugin-vue

# Установить UI библиотеку (например, PrimeVue)
npm install primevue primeicons
```

### 6. Создание Vue компонентов
```
resources/js/
├── components/
│   ├── Auth/
│   │   └── LoginForm.vue
│   ├── Payments/
│   │   ├── PaymentList.vue
│   │   ├── PaymentForm.vue
│   │   └── PaymentDetails.vue
│   ├── Exchanges/
│   │   ├── ExchangeList.vue
│   │   └── ExchangeForm.vue
│   ├── Credits/
│   │   ├── CreditList.vue
│   │   ├── CreditForm.vue
│   │   └── CreditRepayment.vue
│   ├── Reports/
│   │   ├── DailyReport.vue
│   │   ├── CashierReport.vue
│   │   └── SalaryReport.vue
│   └── Layout/
│       ├── AppHeader.vue
│       ├── AppSidebar.vue
│       └── AppFooter.vue
├── views/
│   ├── Dashboard.vue
│   ├── Payments.vue
│   ├── Exchanges.vue
│   ├── Credits.vue
│   ├── Reports.vue
│   └── Settings.vue
├── router/
│   └── index.js
├── stores/
│   ├── auth.js
│   ├── payment.js
│   ├── exchange.js
│   └── credit.js
└── App.vue
```

### 7. Настройка мобильного API
```php
// В routes/api.php добавить rate limiting для мобильных устройств
Route::middleware(['auth:sanctum', 'throttle:mobile'])->prefix('mobile')->group(function () {
    // Mobile-specific endpoints
});

// В app/Http/Kernel.php
protected $middlewareGroups = [
    'mobile' => [
        'throttle:60,1', // 60 запросов в минуту
        'bindings',
    ],
];
```

---

## 📦 Перенос данных из старой БД

### Создание Seeders для переноса
```bash
php artisan make:seeder MigrateOldDataSeeder
```

**Пример переноса пользователей:**
```php
use App\Models\User;
use Illuminate\Support\Facades\DB;

public function run()
{
    // Подключение к старой БД
    $oldUsers = DB::connection('old_mysql')->table('users')->get();

    foreach ($oldUsers as $oldUser) {
        User::create([
            'login' => $oldUser->login,
            'full_name' => $oldUser->name,
            'password' => bcrypt('temporary_password'), // Пользователи должны сбросить пароль
            'position' => $oldUser->position === 'Супер Админ' ? 'admin' : 'cashier',
            'status' => $oldUser->status === 'Активный' ? 'active' : 'blocked',
            'branch' => $oldUser->branch,
            'salary_percentage' => $oldUser->salary ?? 0,
        ]);
    }
}
```

### Настройка подключения к старой БД
В `config/database.php`:
```php
'old_mysql' => [
    'driver' => 'mysql',
    'host' => '83.69.139.168',
    'port' => '3306',
    'database' => 'uz123_kokand',
    'username' => 'uz123_SuperAdmin',
    'password' => 'Bankir2721',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
],
```

---

## 🧪 Тестирование

### Unit тесты
```bash
php artisan make:test PaymentTest --unit
php artisan make:test ExchangeTest --unit
php artisan make:test CreditTest --unit
```

### Feature тесты
```bash
php artisan make:test PaymentApiTest
php artisan make:test AuthenticationTest
php artisan make:test AuthorizationTest
```

---

## 📱 Мобильное приложение

### Рекомендации по разработке мобильного API
1. Использовать JSON:API спецификацию
2. Реализовать пагинацию для больших списков
3. Добавить поддержку offline режима (синхронизация)
4. Использовать Push-уведомления для важных событий
5. Реализовать биометрическую аутентификацию

---

## 🔒 Безопасность

### Чек-лист безопасности
- ✅ Использование prepared statements (Eloquent ORM)
- ✅ Bcrypt для паролей
- ✅ CSRF защита (встроенная в Laravel)
- ✅ XSS защита через Blade escaping
- ✅ SQL injection защита через Eloquent
- ⏳ Rate limiting для API
- ⏳ CORS настройка
- ⏳ API token expiration
- ⏳ Input validation через Form Requests
- ⏳ Authorization через Policies

---

## 📈 Производительность

### Оптимизация
1. Использовать Eager Loading для отношений
2. Кэшировать курсы валют
3. Индексы на часто используемых полях (date, cashier_id, status)
4. Использовать Redis для сессий и кэша
5. Оптимизировать N+1 запросы

---

## 🛠️ Команды для разработки

```bash
# Запустить локальный сервер
php artisan serve

# Запустить Vite (для Vue.js)
npm run dev

# Собрать production build
npm run build

# Запустить миграции
php artisan migrate

# Откатить последнюю миграцию
php artisan migrate:rollback

# Очистить кэш
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Запустить тесты
php artisan test

# Создать нового пользователя через Tinker
php artisan tinker
>>> $user = User::create(['login' => 'admin', 'full_name' => 'Администратор', 'password' => bcrypt('password'), 'position' => 'admin']);
>>> $user->assignRole('admin');
```

---

## 📞 Поддержка

При возникновении вопросов или проблем:
1. Проверьте логи: `storage/logs/laravel.log`
2. Используйте `php artisan tinker` для отладки
3. Проверьте документацию Laravel: https://laravel.com/docs

---

## 📝 Контрольный список миграции

### Бэкенд
- [x] Создать Laravel 11 проект
- [x] Установить необходимые пакеты
- [x] Создать миграции базы данных
- [x] Создать Eloquent модели
- [ ] Создать Seeders (роли, права, тестовые данные)
- [ ] Создать API контроллеры
- [ ] Создать Form Requests
- [ ] Создать API Resources
- [ ] Создать Policies для авторизации
- [ ] Настроить маршруты API
- [ ] Добавить Rate Limiting
- [ ] Написать тесты
- [ ] Создать систему отчетов
- [ ] Реализовать экспорт в Excel/PDF

### Фронтенд
- [ ] Установить Vue.js 3
- [ ] Настроить Vue Router
- [ ] Настроить Pinia (state management)
- [ ] Создать компоненты аутентификации
- [ ] Создать компоненты для платежей
- [ ] Создать компоненты для обмена валют
- [ ] Создать компоненты для кредитов
- [ ] Создать компоненты для инкассации
- [ ] Создать компоненты отчетов
- [ ] Реализовать адаптивный дизайн
- [ ] Добавить валидацию форм
- [ ] Реализовать обработку ошибок
- [ ] Добавить loading states
- [ ] Оптимизировать производительность

### Деплой
- [ ] Настроить production окружение
- [ ] Настроить базу данных MySQL на продакшене
- [ ] Перенести данные из старой БД
- [ ] Настроить HTTPS
- [ ] Настроить резервное копирование
- [ ] Настроить мониторинг
- [ ] Провести нагрузочное тестирование
- [ ] Обучить пользователей

---

**Последнее обновление:** 31 декабря 2025
