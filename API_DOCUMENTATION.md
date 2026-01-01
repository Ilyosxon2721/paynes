# 📡 API Documentation - Anesi Kassa

## Обзор

Полная документация REST API для системы Anesi Kassa на Laravel 11.

**Base URL:** `http://localhost:8000/api`

**Аутентификация:** Bearer Token (Laravel Sanctum)

---

## 🔐 Аутентификация

### POST /login
Авторизация пользователя

**Request:**
```json
{
  "login": "admin",
  "password": "admin123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Авторизация прошла успешно.",
  "data": {
    "user": {
      "id": 1,
      "login": "admin",
      "full_name": "Администратор системы",
      "position": "admin",
      "status": "active",
      "branch": "Главный офис",
      "salary_percentage": "0.00",
      "roles": ["admin"],
      "permissions": ["payments.create", "payments.view", ...],
      "is_admin": true,
      "created_at": "2025-12-31 12:00:00",
      "updated_at": "2025-12-31 12:00:00"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### POST /logout
Выход из системы

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Вы успешно вышли из системы."
}
```

### GET /user
Получить данные текущего пользователя

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "login": "admin",
    "full_name": "Администратор системы",
    ...
  }
}
```

---

## 💳 Платежи (Payments)

### GET /payments
Список всех платежей

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `status` (optional) - фильтр по статусу: pending, confirmed, deleted, processed
- `date` (optional) - фильтр по дате: YYYY-MM-DD
- `cashier_id` (optional) - фильтр по кассиру
- `page` (optional) - номер страницы

**Response (200):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "list_number": "12345",
        "random_number": "123456",
        "date": "2025-12-31",
        "time": "14:30:00",
        "payment_type": {
          "id": 1,
          "name": "Коммунальные услуги",
          "commission_percentage": "2.00",
          "commission_fixed": "0.00"
        },
        "payer_name": "Иванов Иван Иванович",
        "purpose": "Оплата за декабрь 2025",
        "amount": "100000.00",
        "commission": "2000.00",
        "total": "102000.00",
        "payment_method": "cash",
        "currency": "UZS",
        "status": "pending",
        "cashier": {
          "id": 2,
          "full_name": "Кассир Иванов Иван Иванович"
        },
        "formatted_total": "102 000.00",
        "created_at": "2025-12-31 14:30:15",
        "updated_at": "2025-12-31 14:30:15"
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 20,
      "total": 95
    }
  }
}
```

### POST /payments
Создать новый платеж

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "payment_type_id": 1,
  "payer_name": "Иванов Иван Иванович",
  "purpose": "Оплата коммунальных услуг",
  "amount": 100000,
  "payment_method": "cash",
  "currency": "UZS",
  "list_number": "12345"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Платеж успешно создан.",
  "data": {
    "id": 1,
    ...
  }
}
```

### GET /payments/{id}
Получить один платеж

**Headers:** `Authorization: Bearer {token}`

**Response (200):** Аналогично элементу из списка

### PUT /payments/{id}
Обновить платеж

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "payer_name": "Петров Петр Петрович",
  "amount": 150000
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Платеж успешно обновлен.",
  "data": {...}
}
```

### DELETE /payments/{id}
Удалить платеж (soft delete)

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Платеж успешно удален."
}
```

### POST /payments/{id}/confirm
Подтвердить платеж

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Платеж успешно подтвержден.",
  "data": {...}
}
```

### POST /payments/{id}/duplicate
Создать дубликат платежа

**Headers:** `Authorization: Bearer {token}`

**Response (201):**
```json
{
  "success": true,
  "message": "Дубликат платежа создан.",
  "data": {...}
}
```

---

## 📋 Типы платежей (Payment Types)

### GET /payment-types
Список всех типов платежей

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Коммунальные услуги",
      "organization": "АО \"Узбекэнерго\"",
      "bank": "Национальный банк Узбекистана",
      "account_number": "20208000705210001001",
      "mfo": "00014",
      "inn": "200574890",
      "commission_percentage": "2.00",
      "commission_fixed": "0.00",
      "type": "utility"
    }
  ]
}
```

### POST /payment-types
Создать новый тип платежа (только admin)

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "name": "Новый тип платежа",
  "organization": "ООО \"Организация\"",
  "bank": "Банк",
  "account_number": "12345678901234567890",
  "mfo": "00014",
  "inn": "123456789",
  "commission_percentage": 1.5,
  "commission_fixed": 0,
  "type": "other"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Тип платежа успешно создан.",
  "data": {...}
}
```

### GET /payment-types/{id}
Получить один тип платежа

### PUT /payment-types/{id}
Обновить тип платежа (только admin)

### DELETE /payment-types/{id}
Удалить тип платежа (только если нет связанных платежей)

---

## 💱 Обмен валют (Exchanges)

### GET /exchanges
Список обменов валют

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `type` (optional) - фильтр по типу: buy, sell
- `date` (optional) - фильтр по дате: YYYY-MM-DD
- `page` (optional) - номер страницы

**Response (200):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "date": "2025-12-31",
        "time": "14:30:00",
        "usd_amount": "100.00",
        "uzs_amount": "1250000.00",
        "type": "buy",
        "rate": "12500.00",
        "cashier": {
          "id": 2,
          "full_name": "Кассир Иванов Иван Иванович"
        },
        "created_at": "2025-12-31 14:30:15"
      }
    ],
    "meta": {...}
  }
}
```

### POST /exchanges
Создать обмен валюты

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "usd_amount": 100,
  "type": "buy"
}
```

**Note:** Курс берется автоматически из последнего курса валют. UZS сумма рассчитывается автоматически.

**Response (201):**
```json
{
  "success": true,
  "message": "Обмен валюты успешно выполнен.",
  "data": {...}
}
```

### GET /exchanges/{id}
Получить один обмен

### DELETE /exchanges/{id}
Удалить обмен (soft delete)

---

## 💰 Кредиты (Credits)

### GET /credits
Список кредитов

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `status` (optional) - фильтр по статусу: pending, confirmed, deleted
- `page` (optional) - номер страницы

**Response (200):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "date": "2025-12-31",
        "time": "14:30:00",
        "recipient": "Иванов Иван Иванович",
        "account_number": "29801000112345678901234567890001",
        "branch": "Филиал №1",
        "debit": "1000000.00",
        "credit": "0.00",
        "confirmed_by": null,
        "status": "pending",
        "cashier": {
          "id": 2,
          "full_name": "Кассир Иванов Иван Иванович"
        },
        "created_at": "2025-12-31 14:30:15",
        "updated_at": "2025-12-31 14:30:15"
      }
    ],
    "meta": {...}
  }
}
```

### POST /credits
Создать заявку на кредит

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "recipient": "Иванов Иван Иванович",
  "branch": "Филиал №1",
  "debit": 1000000,
  "credit": 0
}
```

**Note:** Номер счета генерируется автоматически по формуле.

**Response (201):**
```json
{
  "success": true,
  "message": "Кредит успешно создан.",
  "data": {...}
}
```

### GET /credits/{id}
Получить один кредит

### PUT /credits/{id}
Обновить кредит

### DELETE /credits/{id}
Удалить кредит (soft delete)

### POST /credits/{id}/confirm
Подтвердить кредит

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "message": "Кредит успешно подтвержден.",
  "data": {...}
}
```

### POST /credits/repay
Погасить кредит

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "credit_id": 1,
  "amount": 500000
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Платеж по кредиту успешно выполнен.",
  "data": {...}
}
```

---

## 💵 Инкассация (Incashes)

### GET /incashes
Список инкассаций

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `account_number` (optional) - фильтр по счету: 001, 002, 003, 840
- `date` (optional) - фильтр по дате: YYYY-MM-DD
- `page` (optional) - номер страницы

**Response (200):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "date": "2025-12-31",
        "time": "14:30:00",
        "account_number": "001",
        "amount": "5000000.00",
        "type": "Расход",
        "status": "pending",
        "cashier": {
          "id": 2,
          "full_name": "Кассир Иванов Иван Иванович"
        },
        "created_at": "2025-12-31 14:30:15"
      }
    ],
    "meta": {...}
  }
}
```

### POST /incashes
Создать инкассацию

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "account_number": "001",
  "amount": 5000000,
  "type": "Расход"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Инкассация успешно создана.",
  "data": {...}
}
```

### GET /incashes/{id}
Получить одну инкассацию

### DELETE /incashes/{id}
Удалить инкассацию (soft delete)

---

## 📈 Курсы валют (Rates)

### GET /rates
Список курсов валют

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 3,
      "buy_rate": "12500.00",
      "sell_rate": "12600.00",
      "date": "2025-12-31",
      "created_at": "2025-12-31 10:00:00"
    },
    {
      "id": 2,
      "buy_rate": "12475.00",
      "sell_rate": "12575.00",
      "date": "2025-12-30",
      "created_at": "2025-12-30 10:00:00"
    }
  ]
}
```

### GET /rates/latest
Получить последний курс

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 3,
    "buy_rate": "12500.00",
    "sell_rate": "12600.00",
    "date": "2025-12-31",
    "created_at": "2025-12-31 10:00:00"
  }
}
```

### POST /rates
Создать курс валюты

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "buy_rate": 12500,
  "sell_rate": 12600,
  "date": "2025-12-31"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Курс валюты успешно создан.",
  "data": {...}
}
```

### GET /rates/{id}
Получить один курс

### PUT /rates/{id}
Обновить курс

### DELETE /rates/{id}
Удалить курс (только если не последний)

---

## ⚠️ Коды ошибок

- **200** - OK (успешный запрос)
- **201** - Created (ресурс создан)
- **400** - Bad Request (ошибка валидации)
- **401** - Unauthorized (не авторизован)
- **403** - Forbidden (нет доступа)
- **404** - Not Found (ресурс не найден)
- **422** - Unprocessable Entity (ошибка валидации)
- **500** - Internal Server Error (внутренняя ошибка сервера)

**Формат ошибки:**
```json
{
  "success": false,
  "message": "Описание ошибки",
  "errors": {
    "field_name": ["Сообщение об ошибке"]
  }
}
```

---

## 🔒 Авторизация и права доступа

Система использует роли и права (Spatie Permission):

### Роли:
- **admin** - Супер администратор (полный доступ)
- **cashier** - Кассир (ограниченный доступ)

### Права:
- **payments**: create, view, update, delete, confirm
- **exchanges**: create, view, delete
- **credits**: create, view, update, delete, confirm, repay
- **incashes**: create, view, delete
- **rates**: create, view, update, delete
- **users**: view, create, update, delete, block
- **reports**: view-all, view-own, export

---

## 📝 Примечания

1. Все даты возвращаются в формате `YYYY-MM-DD HH:MM:SS`
2. Все суммы в decimal формате с 2 знаками после запятой
3. Пагинация по умолчанию - 20 записей на страницу
4. Токен Sanctum действует до явного logout
5. Все запросы (кроме login) требуют header `Authorization: Bearer {token}`
6. Автоматически устанавливаются поля: date, time, cashier_id
7. Комиссия для платежей рассчитывается автоматически
8. Курс для обмена валют берется автоматически из последнего курса

---

**Версия API:** 1.0.0
**Последнее обновление:** 31 декабря 2025
