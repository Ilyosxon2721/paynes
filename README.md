# 🏦 Anesi Kassa - Laravel Edition

Современная кассовая система на Laravel 11 + PHP 8.3 + Vue.js 3 SPA

## ✨ Особенности

- 🔐 **Аутентификация** - Laravel Sanctum с Bearer токенами
- 👥 **Роли и права** - Spatie Laravel Permission
- 💳 **Платежи** - Полное управление платежами с комиссиями
- 💱 **Обмен валют** - Покупка/продажа USD/UZS
- 💰 **Кредиты** - Управление кредитами с погашением
- 💵 **Инкассация** - Операции по счетам
- 📈 **Курсы валют** - Управление курсами обмена
- 🎨 **Современный UI** - Vue.js 3 SPA с красивым дизайном
- 📱 **Адаптивный дизайн** - Готово к мобильной версии

## 🚀 Быстрый старт

### Требования
- PHP 8.3+
- Composer
- Node.js 18+ и npm
- MySQL 8.0+ или SQLite
- Git

### Установка

1. **Клонировать репозиторий**
```bash
cd /Users/ilyosxon/Downloads/admin-kassa-laravel
```

2. **Установить зависимости**
```bash
composer install
npm install
```

3. **Настроить окружение**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Настроить базу данных**

Для разработки (SQLite уже настроен):
```bash
touch database/database.sqlite
```

Для продакшена (отредактируйте .env):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin_kassa_laravel
DB_USERNAME=root
DB_PASSWORD=
```

5. **Запустить миграции**
```bash
php artisan migrate
```

6. **Создать роли и права**
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

7. **Заполнить тестовыми данными**
```bash
php artisan db:seed
```

Это создаст:
- Роли и права (admin, cashier)
- Типы платежей (7 шт)
- Тестовых пользователей:
  - admin / admin123
  - cashier1 / cashier123
  - cashier2 / cashier123
- Курсы валют

8. **Собрать фронтенд**
```bash
npm run build
```

9. **Запустить сервер**
```bash
# Режим разработки
php artisan serve
# И в другом терминале:
npm run dev

# Или только production
php artisan serve
```

Приложение будет доступно по адресу: http://localhost:8000

---

## 📚 Документация

- **[Руководство по миграции](MIGRATION_GUIDE.md)** - полная документация по миграции
- **[API Documentation](API_DOCUMENTATION.md)** - полная документация REST API
- **[Frontend Guide](FRONTEND_GUIDE.md)** - руководство по Vue.js фронтенду
- **[Quick Start](QUICKSTART.md)** - быстрый старт за 5 минут

---

## 🏗️ Архитектура

### Бэкенд
- **Framework:** Laravel 11
- **PHP:** 8.3
- **Database:** MySQL / SQLite
- **Authentication:** Laravel Sanctum
- **Permissions:** Spatie Laravel Permission

### Фронтенд
- **Framework:** Vue.js 3 (Composition API)
- **Build Tool:** Vite 6
- **State Management:** Pinia
- **Router:** Vue Router 4
- **HTTP Client:** Axios
- **UI:** Custom CSS (градиенты, shadows, animations)

---

## 📦 Основные модули

### 1. Платежи (Payments)
- Создание платежей
- Комиссии (процент + фиксированная)
- Наличные / безналичные
- Статусы: pending, confirmed, deleted, processed
- Дубликаты чеков

### 2. Обмен валют (Exchanges)
- Покупка / продажа USD
- Автоматический расчет по курсу
- История операций

### 3. Кредиты (Credits)
- Заявки на кредит
- Погашение кредитов
- Генерация номеров счетов
- Подтверждение администратором

### 4. Инкассация (Incash)
- Операции расхода/прихода
- Расчетные счета (001, 002, 003, 840)
- Учет по кассирам

### 5. Отчеты (Reports)
- Дневные отчеты
- Отчеты по кассирам
- Расчет зарплаты
- Экспорт в Excel/PDF

---

## 🗄️ Структура базы данных

### Основные таблицы
- `users` - пользователи
- `payment_types` - справочник типов платежей
- `payments` - платежи
- `exchanges` - обмен валют
- `credits` - кредиты
- `incashes` - инкассация
- `rates` - курсы валют

Подробная схема БД доступна в [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md#-структура-базы-данных)

---

## 🔐 Роли и права

### Роли
- **admin** - Супер Админ (полный доступ)
- **cashier** - Кассир (ограниченный доступ)

### Основные права
- `payments.*` - управление платежами
- `exchanges.*` - операции обмена валют
- `credits.*` - управление кредитами
- `incashes.*` - операции инкассации
- `rates.*` - управление курсами
- `reports.*` - просмотр отчетов
- `users.*` - управление пользователями

---

## 🛠️ Полезные команды

```bash
# Разработка
php artisan serve              # Запустить сервер
php artisan migrate:fresh      # Пересоздать БД
php artisan db:seed            # Заполнить тестовыми данными
php artisan tinker             # Консоль Laravel

# Создание компонентов
php artisan make:model ModelName
php artisan make:controller ControllerName --api
php artisan make:request RequestName
php artisan make:resource ResourceName
php artisan make:migration create_table_name

# Тестирование
php artisan test               # Запустить все тесты
php artisan test --filter=TestName  # Запустить конкретный тест

# Очистка кэша
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Производительность
php artisan route:cache
php artisan config:cache
php artisan view:cache
php artisan optimize
```

---

## 📝 Примеры использования

### Создание платежа
```php
use App\Models\Payment;
use App\Models\PaymentType;

$paymentType = PaymentType::find(1);
$commission = $paymentType->calculateCommission(1000);

$payment = Payment::create([
    'date' => now()->toDateString(),
    'time' => now()->toTimeString(),
    'payment_type_id' => $paymentType->id,
    'payer_name' => 'Иванов Иван Иванович',
    'purpose' => 'Оплата коммунальных услуг',
    'amount' => 1000,
    'commission' => $commission,
    'total' => 1000 + $commission,
    'payment_method' => 'cash',
    'currency' => 'UZS',
    'status' => 'pending',
    'cashier_id' => auth()->id(),
]);
```

### Создание обмена валют
```php
use App\Models\Exchange;
use App\Models\Rate;

$rate = Rate::getLatest();

$exchange = Exchange::create([
    'date' => now()->toDateString(),
    'time' => now()->toTimeString(),
    'usd_amount' => 100,
    'uzs_amount' => 100 * $rate->buy_rate,
    'type' => 'buy',
    'rate' => $rate->buy_rate,
    'cashier_id' => auth()->id(),
]);
```

### Получение отчета за день
```php
use App\Models\Payment;

$payments = Payment::byDate('2025-12-31')
    ->confirmed()
    ->get();

$total = $payments->sum('total');
$commission = $payments->sum('commission');
```

---

## 🧪 Тестирование

```bash
# Запустить все тесты
php artisan test

# Запустить с покрытием кода
php artisan test --coverage

# Запустить конкретный тест
php artisan test --filter=PaymentTest
```

---

## 🚢 Деплой на продакшен

1. **Настроить .env для продакшена**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-host
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

2. **Оптимизировать приложение**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

3. **Настроить права доступа**
```bash
chmod -R 755 storage bootstrap/cache
```

4. **Запустить миграции**
```bash
php artisan migrate --force
```

---

## 🤝 Вклад в проект

1. Форкните репозиторий
2. Создайте feature-ветку (`git checkout -b feature/AmazingFeature`)
3. Закоммитьте изменения (`git commit -m 'Add some AmazingFeature'`)
4. Запушьте в ветку (`git push origin feature/AmazingFeature`)
5. Откройте Pull Request

---

## 📄 Лицензия

Проприетарное ПО - Все права защищены © 2025 Anesi Kassa

---

## 📞 Контакты

- **Email:** support@anesikassa.uz
- **Telegram:** @anesikassa

---

## ✨ Благодарности

- Laravel Framework
- Vue.js Team
- Spatie для Laravel Permission
- Все контрибьюторы

---

**Версия:** 2.0.0 (Laravel Edition)
**Последнее обновление:** 31 декабря 2025
