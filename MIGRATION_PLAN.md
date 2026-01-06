# План Миграции Старой БД в Laravel

**Дата:** 2026-01-02
**Версия:** 1.0

---

## 1. Создание Недостающих Миграций

### 1.1 Таблица `agents`
```bash
php artisan make:migration create_agents_table
```

**Поля:**
- id (PK)
- login (уникальный)
- password (bcrypt, конвертировать из MD5)
- full_name
- added_by
- position
- date, time (created)
- reward (decimal)
- branch
- status (enum: active, closed)
- phone
- timestamps, softDeletes

### 1.2 Таблица `fines`
```bash
php artisan make:migration create_fines_table
```

### 1.3 Таблица `calculations`
```bash
php artisan make:migration create_calculations_table
```

### 1.4 Таблица `agent_cashins`
```bash
php artisan make:migration create_agent_cashins_table
```

### 1.5 Таблица `budget_payment_types`
```bash
php artisan make:migration create_budget_payment_types_table
```

### 1.6 Таблица `communal_payment_types`
```bash
php artisan make:migration create_communal_payment_types_table
```

---

## 2. Модификация Существующих Миграций

### 2.1 Расширить таблицу `payments`
```php
// Добавить поля:
$table->string('city')->nullable();
$table->string('region')->nullable();
$table->decimal('cash_back', 10, 2)->nullable();
$table->foreignId('agent_id')->nullable()->constrained('agents');
$table->string('payment_system')->nullable(); // payedSysteam
```

### 2.2 Расширить таблицу `users`
```php
// Добавить поля:
$table->string('branch')->nullable();
$table->decimal('reward_percentage', 5, 2)->default(0);
$table->string('added_by')->nullable();
```

### 2.3 Расширить таблицу `credits`
```php
// Добавить поля:
$table->string('xr')->nullable(); // хисоб рақам
$table->string('branch')->nullable();
$table->decimal('debit', 15, 2);
$table->decimal('credit', 15, 2);
$table->string('confirmed_by')->nullable();
```

### 2.4 Расширить таблицу `rates`
```php
// Изменить структуру для совместимости:
$table->integer('usd_buy');
$table->integer('usd_sell');
$table->string('updated_by');
```

---

## 3. Скрипт Миграции Данных

### 3.1 Создать Seeder для миграции
```bash
php artisan make:seeder MigrateOldDatabaseSeeder
```

### 3.2 Алгоритм миграции:

#### Шаг 1: Мигрировать пользователей
```php
// Конвертировать MD5 пароли → bcrypt
// Мапинг полей: name → full_name, data+clock → created_at
// Мапинг статусов: "Активный" → "active", "Закрыт" → "inactive"
```

#### Шаг 2: Мигрировать агентов
```php
// Создать записи в таблице agents
```

#### Шаг 3: Мигрировать типы платежей
```php
// pays → payment_types
// pays_byudjet → budget_payment_types
// pays_comunal → communal_payment_types
```

#### Шаг 4: Мигрировать курсы
```php
// Конвертировать USDbuy/USDsell → buy_rate/sell_rate
```

#### Шаг 5: Мигрировать платежи (112,000+ записей!)
```php
// Мапинг полей:
// data → date
// clock → time
// summa → amount
// komissiya → commission
// jami → total
// type → payment_type_id (найти по имени)
// FIO → cashier_id (найти по имени)
// valyuta → currency (UZS/USD)
// status: "Подтвержден" → "confirmed", "Удален" → "deleted"
```

#### Шаг 6: Мигрировать обмены валют
```php
// Конвертировать структуру exchange
```

#### Шаг 7: Мигрировать кредиты
```php
// Конвертировать структуру credit
```

#### Шаг 8: Мигрировать инкассации
```php
// Конвертировать структуру incash
```

---

## 4. Маппинг Статусов

### Payments:
- `"Подтвержден"` → `"confirmed"`
- `"Удален"` → `"deleted"`
- `""` (пустой) → `"pending"`

### Users:
- `"Активный"` → `"active"`
- `"Закрыт"` → `"inactive"`

### Positions:
- `"Супер Админ"` → `"admin"`
- `"Пользовательский"` → `"cashier"`

---

## 5. Безопасность

### ⚠️ КРИТИЧНО: Пароли
Старые MD5 пароли **НЕЛЬЗЯ** конвертировать в bcrypt!

**Решение:**
1. Создать временный флаг `legacy_password` в users
2. При первом входе попросить сменить пароль
3. ИЛИ: Установить всем единый временный пароль и отправить на почту

---

## 6. Последовательность Выполнения

```bash
# 1. Создать все новые миграции
php artisan make:migration create_agents_table
php artisan make:migration create_fines_table
php artisan make:migration add_old_db_fields_to_payments
php artisan make:migration add_old_db_fields_to_users

# 2. Запустить миграции
php artisan migrate

# 3. Создать Seeder для импорта данных
php artisan make:seeder MigrateOldDatabaseSeeder

# 4. Подключить старую БД в config/database.php
'old_mysql' => [
    'driver' => 'mysql',
    'host' => '83.69.139.168',
    'database' => 'uz123_kokand_old', // Копия старой БД
    'username' => 'uz123_SuperAdmin',
    'password' => 'Bankir2721',
],

# 5. Запустить импорт (может занять 10-30 минут!)
php artisan db:seed --class=MigrateOldDatabaseSeeder

# 6. Проверить данные
php artisan tinker
>>> \App\Models\Payment::count() // Должно быть 112,000+
>>> \App\Models\User::count()
>>> \App\Models\Agent::count()
```

---

## 7. Валидация После Миграции

### Чеклист:
- [ ] Все 112,000+ платежей импортированы
- [ ] Все пользователи импортированы с правильными ролями
- [ ] Курсы валют на месте
- [ ] Агенты импортированы
- [ ] Связи FK правильные (payment → payment_type, payment → cashier)
- [ ] Суммы совпадают (sum(amount) старой БД == sum(amount) новой БД)
- [ ] Статусы конвертированы правильно
- [ ] Филиалы (branches) сохранены

---

## 8. Откат (если что-то пошло не так)

```bash
# Удалить все данные
php artisan migrate:fresh

# Восстановить из бэкапа
mysql -u uz123_SuperAdmin -p uz123_kokand < backup.sql
```

---

## 9. Тестовый Импорт

**Рекомендация:** Сначала протестировать на первых 100 записях!

```php
// В Seeder добавить:
if (app()->environment('local')) {
    $oldPayments->limit(100); // Тестовый режим
}
```

---

## 10. Производительность

112,000+ записей платежей - это **большой объем**!

**Оптимизация:**
```php
// Использовать chunk для больших таблиц
DB::connection('old_mysql')
    ->table('payment')
    ->orderBy('id')
    ->chunk(1000, function ($payments) {
        foreach ($payments as $payment) {
            // Миграция одной записи
        }
    });
```

**Использовать insert вместо create:**
```php
// Вместо:
Payment::create([...]); // Медленно!

// Использовать:
Payment::insert([...]); // Быстро!
// Но без events и timestamps автоматом
```

---

## Итого: Что делать дальше?

1. ✅ Создать недостающие миграции (agents, fines, calculations)
2. ✅ Расширить существующие миграции (payments, users, credits)
3. ✅ Создать Seeder для импорта данных
4. ✅ Запустить тестовый импорт (100 записей)
5. ✅ Проверить результаты
6. ✅ Запустить полный импорт (112,000+ записей)
7. ✅ Валидировать данные
8. ✅ Обновить пароли пользователей

**Ожидаемое время:** 4-6 часов работы
