# Сравнение Старой и Новой БД

**Дата:** 2026-01-02

---

## 📊 Статистика

### Старая БД (uz123_kokand):
- **Таблиц:** 13
- **Платежей:** 112,000+
- **Пользователей:** 11
- **Агентов:** 2
- **Обменов:** ~1,500
- **Кредитов:** ~800
- **Инкассаций:** ~1,400

### Новая Laravel БД:
- **Таблиц:** 11
- **Данных:** 0 (пустая)

---

## 🔍 Детальное Сравнение Таблиц

### 1. `users` (Пользователи)

#### Старая структура:
```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(100) NOT NULL,
  `pass` varchar(50) NOT NULL,              -- MD5!
  `name` text NOT NULL,
  `added` varchar(50) NOT NULL,
  `position` text NOT NULL,                  -- Текст: "Супер Админ"
  `data` date NOT NULL,
  `clock` time NOT NULL,
  `reward` int(3) NOT NULL,                  -- Процент вознаграждения
  `branch` text NOT NULL,                    -- Филиал!
  `status` text NOT NULL                     -- "Активный", "Закрыт"
)
```

#### Новая структура (Laravel):
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('login')->unique();
    $table->string('full_name');
    $table->string('password');                // bcrypt!
    $table->enum('position', ['admin', 'cashier', 'manager']);
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
    $table->rememberToken();
    $table->softDeletes();
});
```

#### ❌ Отсутствующие поля:
- `branch` (филиал) - **КРИТИЧНО!**
- `reward` (процент вознаграждения) - **КРИТИЧНО!**
- `added_by` (кто добавил)

#### ⚠️ Несовместимость:
- Пароли: MD5 → bcrypt (нужна миграция)
- Статусы: "Активный" → "active"
- Должности: "Супер Админ" → "admin"

---

### 2. `payment` (Платежи) - ОСНОВНАЯ ТАБЛИЦА!

#### Старая структура (20 полей):
```sql
CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `random` varchar(30) NOT NULL,
  `data` date NOT NULL,
  `clock` time NOT NULL,
  `type` text NOT NULL,                      -- Название типа платежа
  `list` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,              -- Плательщик
  `appoint` varchar(100) NOT NULL,           -- Назначение
  `summa` decimal(30,2) NOT NULL,
  `komissiya` decimal(10,2) NOT NULL,
  `jami` decimal(30,2) NOT NULL,
  `typepay` varchar(20) NOT NULL,            -- "Наличные", "Карта"
  `city` text NOT NULL,                      -- ВАЖНО!
  `region` text NOT NULL,                    -- ВАЖНО!
  `valyuta` text NOT NULL,                   -- "сум", "доллар"
  `status` varchar(255) NOT NULL,            -- "Подтвержден", "Удален"
  `paysid` int(10) NOT NULL,                 -- FK к pays
  `payedSysteam` varchar(50) NOT NULL,       -- "UzCard", "Humo"
  `FIO` varchar(100) NOT NULL,               -- ФИО кассира
  `cashBack` decimal(30,2),                  -- Кэшбэк!
  `agent_id` int(11) NOT NULL                -- FK к agents!
)
```

#### Новая структура (Laravel):
```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->string('list_number')->nullable();
    $table->string('random_number')->nullable();
    $table->date('date');
    $table->time('time');
    $table->foreignId('payment_type_id')->constrained('payment_types');
    $table->string('payer_name');
    $table->text('purpose')->nullable();
    $table->decimal('amount', 15, 2);
    $table->decimal('commission', 15, 2)->default(0);
    $table->decimal('total', 15, 2);
    $table->enum('payment_method', ['cash', 'card'])->default('cash');
    $table->enum('currency', ['UZS', 'USD'])->default('UZS');
    $table->enum('status', ['pending', 'confirmed', 'deleted', 'processed']);
    $table->foreignId('cashier_id')->constrained('users');
    $table->timestamps();
    $table->softDeletes();
});
```

#### ❌ Отсутствующие критичные поля:
- `city` - Город платежа
- `region` - Регион платежа
- `cash_back` - Кэшбэк клиенту
- `agent_id` - Связь с агентом
- `payment_system` - UzCard/Humo/etc

#### 📝 Маппинг полей:
| Старая БД | Новая Laravel | Конверсия |
|-----------|---------------|-----------|
| `data` | `date` | Прямой |
| `clock` | `time` | Прямой |
| `random` | `random_number` | Прямой |
| `list` | `list_number` | Прямой |
| `name` | `payer_name` | Прямой |
| `appoint` | `purpose` | Прямой |
| `summa` | `amount` | Прямой |
| `komissiya` | `commission` | Прямой |
| `jami` | `total` | Прямой |
| `type` | `payment_type_id` | Найти ID по имени |
| `FIO` | `cashier_id` | Найти ID кассира |
| `valyuta` | `currency` | "сум" → "UZS", "доллар" → "USD" |
| `status` | `status` | "Подтвержден" → "confirmed" |
| `typepay` | `payment_method` | "Наличные" → "cash" |

---

### 3. `agents` (Агенты)

#### Старая структура:
```sql
CREATE TABLE `agents` (
  `id` int(11) NOT NULL,
  `login` varchar(100) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `name` text NOT NULL,
  `added` varchar(50) NOT NULL,
  `position` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `reward` decimal(10,2) NOT NULL,
  `branch` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `phone` text NOT NULL
)
```

#### Новая структура (Laravel):
❌ **ТАБЛИЦА ОТСУТСТВУЕТ!**

#### ✅ Нужно создать:
```php
Schema::create('agents', function (Blueprint $table) {
    $table->id();
    $table->string('login')->unique();
    $table->string('password');
    $table->string('full_name');
    $table->string('added_by')->nullable();
    $table->string('position')->default('agent');
    $table->decimal('reward_percentage', 5, 2)->default(0);
    $table->string('branch')->nullable();
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->string('phone')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

---

### 4. `exchange` (Обмен валюты)

#### Старая структура:
```sql
CREATE TABLE `exchange` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `USD` text NOT NULL,                       -- Количество долларов
  `UZS` bigint(50) NOT NULL,                 -- Сумма в сумах
  `type` varchar(20) NOT NULL,               -- "Покупка", "Продажа"
  `rate` int(11) NOT NULL,                   -- Курс
  `cashier` varchar(50) NOT NULL             -- ФИО кассира
)
```

#### Новая структура (Laravel):
```php
Schema::create('exchanges', function (Blueprint $table) {
    $table->id();
    $table->date('date');
    $table->time('time');
    $table->enum('from_currency', ['UZS', 'USD']);
    $table->enum('to_currency', ['UZS', 'USD']);
    $table->decimal('from_amount', 15, 2);
    $table->decimal('to_amount', 15, 2);
    $table->decimal('rate', 10, 2);
    $table->enum('type', ['buy', 'sell']);
    $table->foreignId('cashier_id')->constrained('users');
    $table->timestamps();
});
```

#### 📝 Маппинг:
| Старая БД | Новая Laravel | Конверсия |
|-----------|---------------|-----------|
| `USD` | `from_amount` (если покупка) / `to_amount` (если продажа) | Зависит от type |
| `UZS` | `to_amount` (если покупка) / `from_amount` (если продажа) | Зависит от type |
| `type` | `type` | "Покупка" → "buy", "Продажа" → "sell" |
| `cashier` | `cashier_id` | Найти ID по ФИО |

---

### 5. `credit` (Кредиты)

#### Старая структура:
```sql
CREATE TABLE `credit` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `clock` time NOT NULL,
  `recipient` varchar(50) NOT NULL,          -- Получатель
  `xr` varchar(30) NOT NULL,                 -- Хисоб рақам (номер счета)
  `branch` varchar(50) NOT NULL,
  `debit` decimal(30,2) NOT NULL,
  `credit` decimal(30,2) NOT NULL,
  `confirmed` varchar(50) NOT NULL,          -- Кто подтвердил
  `status` varchar(50) NOT NULL
)
```

#### Новая структура (Laravel):
```php
Schema::create('credits', function (Blueprint $table) {
    $table->id();
    $table->string('list_number')->nullable();
    $table->date('date');
    $table->time('time');
    $table->string('client_name');
    $table->decimal('amount', 15, 2);
    $table->enum('currency', ['UZS', 'USD'])->default('UZS');
    $table->enum('status', ['pending', 'confirmed', 'processed'])->default('pending');
    $table->foreignId('cashier_id')->constrained('users');
    $table->timestamps();
});
```

#### ❌ Отсутствующие поля:
- `xr` (номер счета) - **ВАЖНО!**
- `branch` (филиал)
- `debit` (дебет)
- `credit` (кредит)
- `confirmed_by` (кто подтвердил)

---

### 6. `incash` (Инкассация)

#### Старая структура:
```sql
CREATE TABLE `incash` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `xisobraqam` varchar(50) NOT NULL,         -- Номер счета
  `sum` varchar(50) NOT NULL,                -- Сумма
  `fio` varchar(255) NOT NULL,               -- ФИО кассира
  `type` varchar(50) NOT NULL,               -- "Приход", "Расход"
  `status` varchar(50) NOT NULL              -- "Проведен", ""
)
```

#### Новая структура (Laravel):
```php
Schema::create('incashes', function (Blueprint $table) {
    $table->id();
    $table->date('date');
    $table->time('time');
    $table->string('account_number');
    $table->decimal('amount', 15, 2);
    $table->foreignId('cashier_id')->constrained('users');
    $table->enum('type', ['income', 'expense'])->default('income');
    $table->enum('status', ['pending', 'processed'])->default('pending');
    $table->timestamps();
});
```

#### 📝 Маппинг:
| Старая БД | Новая Laravel | Конверсия |
|-----------|---------------|-----------|
| `xisobraqam` | `account_number` | Прямой |
| `sum` | `amount` | Конвертировать в decimal |
| `fio` | `cashier_id` | Найти ID по ФИО |
| `type` | `type` | "Приход" → "income", "Расход" → "expense" |
| `status` | `status` | "Проведен" → "processed", "" → "pending" |

---

### 7. `rate` (Курсы валют)

#### Старая структура:
```sql
CREATE TABLE `rate` (
  `id` int(11) NOT NULL,
  `USDbuy` int(5) NOT NULL,
  `USDsell` int(5) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `user` text NOT NULL
)
```

#### Новая структура (Laravel):
```php
Schema::create('rates', function (Blueprint $table) {
    $table->id();
    $table->enum('currency_from', ['UZS', 'USD']);
    $table->enum('currency_to', ['UZS', 'USD']);
    $table->decimal('buy_rate', 10, 2);
    $table->decimal('sell_rate', 10, 2);
    $table->boolean('is_active')->default(false);
    $table->timestamps();
});
```

#### 📝 Маппинг:
- `USDbuy` → `buy_rate`
- `USDsell` → `sell_rate`
- `currency_from` = 'USD', `currency_to` = 'UZS' (всегда)
- Последняя запись сделать `is_active = true`

---

### 8. `pays` (Типы платежей)

#### Старая структура:
```sql
CREATE TABLE `pays` (
  -- Структура не показана, но есть связь через paysid
)
```

#### Новая структура (Laravel):
```php
Schema::create('payment_types', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->decimal('commission_percentage', 5, 2)->default(0);
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

## 🚨 Таблицы, которых НЕТ в Laravel:

### 1. `calculation` - Финансовые расчеты
❌ **Нужно создать миграцию**

### 2. `cashin_from_agents` - Сборы от агентов
❌ **Нужно создать миграцию**

### 3. `fine` - Штрафы
❌ **Нужно создать миграцию**

### 4. `pays_byudjet` - Бюджетные платежи
❌ **Можно добавить как подтип в `payment_types`**

### 5. `pays_comunal` - Коммунальные платежи
❌ **Можно добавить как подтип в `payment_types`**

---

## 📌 Рекомендации

### Вариант 1: Полная Миграция (Рекомендуется)
✅ Создать недостающие миграции
✅ Расширить существующие таблицы
✅ Написать Seeder для импорта всех данных
✅ Сохранить всю историю (112,000+ платежей)

**Плюсы:**
- Вся история сохранена
- Можно делать аналитику по старым данным
- Непрерывность данных

**Минусы:**
- Требует времени (4-6 часов)
- Риск ошибок при миграции

---

### Вариант 2: Гибридный Подход
✅ Новые данные в Laravel БД
✅ Старые данные оставить в старой БД (read-only)
✅ Создать View для объединенных отчетов

**Плюсы:**
- Быстрее запустить
- Меньше риск

**Минусы:**
- Сложнее делать отчеты
- Две БД для поддержки

---

### Вариант 3: Свежий Старт
✅ Начать с нуля в Laravel БД
✅ Старую БД сохранить для архива

**Плюсы:**
- Чистая структура
- Нет технического долга

**Минусы:**
- ❌ Потеря истории 112,000+ платежей!
- ❌ Нет данных для аналитики

---

## ✅ Мой Совет: ВАРИАНТ 1

Миграция данных обязательна, потому что:

1. **112,000+ платежей** - это критичная история
2. **Финансовые данные** нельзя потерять
3. **Аналитика** требует исторических данных
4. **Отчетность** по всем периодам

---

## Следующие шаги:

1. ✅ Создать миграции для недостающих таблиц
2. ✅ Расширить существующие миграции
3. ✅ Написать Seeder для импорта
4. ✅ Протестировать на 100 записях
5. ✅ Запустить полную миграцию
6. ✅ Валидировать данные

**Начнем?** 🚀
