# 🎨 Frontend Guide - Vue.js 3 SPA

## Обзор

Фронтенд приложения Anesi Kassa построен на современном стеке технологий:

- **Vue.js 3** - Composition API
- **Vue Router 4** - Маршрутизация
- **Pinia** - Управление состоянием
- **Axios** - HTTP клиент
- **Vite** - Сборщик модулей

---

## Структура проекта

```
resources/js/
├── app.js                  # Точка входа приложения
├── App.vue                 # Корневой компонент
├── router/
│   └── index.js           # Конфигурация маршрутов
├── stores/
│   └── auth.js            # Хранилище аутентификации
├── services/
│   └── api.js             # Axios instance с interceptors
├── layouts/
│   └── MainLayout.vue     # Основной layout с sidebar
└── views/
    ├── Auth/
    │   └── Login.vue      # Страница входа
    ├── Dashboard.vue      # Главная панель
    ├── Payments/
    │   ├── Index.vue      # Список платежей
    │   └── Create.vue     # Создание платежа
    ├── PaymentTypes/
    │   └── Index.vue      # Типы платежей
    ├── Exchanges/
    │   ├── Index.vue      # Обмен валют
    │   └── Create.vue     # Новый обмен
    ├── Credits/
    │   ├── Index.vue      # Кредиты
    │   └── Create.vue     # Новый кредит
    ├── Incashes/
    │   ├── Index.vue      # Инкассация
    │   └── Create.vue     # Новая инкассация
    └── Rates/
        └── Index.vue      # Курсы валют
```

---

## Запуск и разработка

### Режим разработки

```bash
# Запуск dev сервера с hot reload
npm run dev
```

Приложение будет доступно по адресу: `http://localhost:8000`

### Production сборка

```bash
# Сборка для production
npm run build

# Просмотр production сборки
php artisan serve
```

---

## Маршрутизация

### Публичные маршруты

- `/login` - Страница входа (доступна только неавторизованным)

### Защищенные маршруты (требуют авторизации)

- `/` - Главная панель
- `/payments` - Список платежей
- `/payments/create` - Создание платежа
- `/payment-types` - Типы платежей (только admin)
- `/exchanges` - Обмен валют
- `/exchanges/create` - Новый обмен
- `/credits` - Кредиты
- `/credits/create` - Новый кредит
- `/incashes` - Инкассация
- `/incashes/create` - Новая инкассация
- `/rates` - Курсы валют (только admin)

### Navigation Guards

В [router/index.js](resources/js/router/index.js) настроена автоматическая проверка:

```javascript
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'Dashboard' });
  } else {
    next();
  }
});
```

---

## Управление состоянием (Pinia)

### Auth Store

Файл: [stores/auth.js](resources/js/stores/auth.js)

#### State

```javascript
const user = ref(null);           // Данные пользователя
const token = ref(null);          // Bearer токен
const loading = ref(false);       // Индикатор загрузки
const error = ref(null);          // Ошибки
```

#### Getters

```javascript
const isAuthenticated = computed(() => !!token.value);
const isAdmin = computed(() => user.value?.position === 'admin');
```

#### Actions

```javascript
// Вход в систему
await authStore.login({ login: 'admin', password: 'admin123' });

// Выход
await authStore.logout();

// Проверка аутентификации
await authStore.checkAuth();

// Проверка прав
authStore.hasPermission('payments.create');
authStore.hasRole('admin');
```

---

## API Клиент

Файл: [services/api.js](resources/js/services/api.js)

### Конфигурация

```javascript
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});
```

### Request Interceptor

Автоматически добавляет Bearer токен к каждому запросу:

```javascript
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
```

### Response Interceptor

Обрабатывает ошибки 401 (Unauthorized):

```javascript
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

### Использование

```javascript
import api from '@/services/api';

// GET запрос
const response = await api.get('/payments');

// POST запрос
const response = await api.post('/payments', data);

// PUT запрос
const response = await api.put('/payments/1', data);

// DELETE запрос
const response = await api.delete('/payments/1');
```

---

## Основные компоненты

### Login.vue

Страница входа в систему с валидацией и обработкой ошибок.

**Особенности:**
- Форма с полями login и password
- Валидация на клиенте
- Отображение ошибок сервера
- Автоматический редирект после успешного входа
- Тестовые данные для быстрого входа

### MainLayout.vue

Основной layout приложения с боковым меню.

**Компоненты:**
- Sidebar с навигационным меню
- Фильтрация пунктов меню по ролям
- Информация о пользователе
- Кнопка выхода
- Основная область контента

### Dashboard.vue

Главная панель с статистикой и быстрыми действиями.

**Элементы:**
- Приветственная карточка
- Статистические карточки (в разработке)
- Кнопки быстрых действий

### Payments/Index.vue

Список платежей с фильтрацией и пагинацией.

**Функции:**
- Фильтрация по статусу и дате
- Пагинация
- Подтверждение платежа
- Дублирование платежа
- Удаление платежа
- Форматирование чисел
- Цветовая индикация статусов

### Payments/Create.vue

Форма создания нового платежа.

**Функции:**
- Выбор типа платежа
- Автоматический расчет комиссии
- Валидация полей
- Отображение итоговой суммы
- Обработка ошибок сервера

---

## Стилизация

### Подход

Используется **Scoped CSS** для каждого компонента:

```vue
<style scoped>
.component-class {
  /* стили применяются только к этому компоненту */
}
</style>
```

### Цветовая палитра

```css
/* Основные цвета */
--primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
--dark: #2c3e50;
--sidebar: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);

/* Статусы */
--success: #28a745;
--warning: #ffc107;
--danger: #dc3545;
--info: #17a2b8;

/* Нейтральные */
--gray-50: #f8f9fa;
--gray-100: #f5f5f5;
--gray-300: #e0e0e0;
```

### Компоненты UI

#### Кнопки

```vue
<button class="btn btn-primary">Создать</button>
<button class="btn btn-secondary">Отмена</button>
<button class="btn btn-danger">Удалить</button>
```

#### Формы

```vue
<div class="form-group">
  <label for="field">Название</label>
  <input id="field" class="form-input" type="text" />
  <span class="error-message">Ошибка</span>
</div>
```

#### Таблицы

```vue
<div class="table-card">
  <table class="data-table">
    <thead>
      <tr><th>Колонка</th></tr>
    </thead>
    <tbody>
      <tr><td>Данные</td></tr>
    </tbody>
  </table>
</div>
```

---

## Обработка ошибок

### Валидация форм

```vue
<script setup>
const errors = ref({});

async function submit() {
  try {
    await api.post('/endpoint', form.value);
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
    }
  }
}
</script>

<template>
  <input :class="{ 'error': errors.field_name }" />
  <span v-if="errors.field_name" class="error-message">
    {{ errors.field_name[0] }}
  </span>
</template>
```

### Глобальные ошибки

```vue
<script setup>
const submitError = ref(null);

async function submit() {
  submitError.value = null;

  try {
    await api.post('/endpoint', data);
  } catch (err) {
    submitError.value = err.response?.data?.message || 'Ошибка сервера';
  }
}
</script>

<template>
  <div v-if="submitError" class="alert alert-error">
    {{ submitError }}
  </div>
</template>
```

---

## Форматирование данных

### Числа

```javascript
function formatNumber(value) {
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
}
```

Использование:
```vue
<template>
  <td>{{ formatNumber(payment.amount) }} UZS</td>
</template>
```

### Даты

```javascript
// Форматирование через API Resource на бэкенде
created_at: this.created_at?->format('Y-m-d H:i:s')
```

---

## Рекомендации по разработке

### Composition API

Используйте Composition API вместо Options API:

```vue
<script setup>
import { ref, computed, onMounted } from 'vue';

const count = ref(0);
const double = computed(() => count.value * 2);

onMounted(() => {
  console.log('Component mounted');
});
</script>
```

### Реактивность

```javascript
// Правильно
const form = ref({ name: '' });
form.value.name = 'New name';

// Неправильно
let form = { name: '' };
form.name = 'New name'; // Не реактивно!
```

### Async/Await

```javascript
async function loadData() {
  loading.value = true;

  try {
    const response = await api.get('/data');
    items.value = response.data.data;
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}
```

### Computed Properties

```javascript
// Используйте computed для вычисляемых значений
const total = computed(() => {
  return form.value.amount + calculatedCommission.value;
});

// Не делайте так:
function getTotal() {
  return form.value.amount + calculatedCommission.value;
}
```

---

## Следующие шаги

### Что уже реализовано

- ✅ Структура Vue.js 3 приложения
- ✅ Роутинг с защитой маршрутов
- ✅ Аутентификация через Sanctum
- ✅ Главная панель
- ✅ Логин страница
- ✅ Список и создание платежей
- ✅ Просмотр типов платежей
- ✅ Базовые компоненты для других модулей

### Что необходимо доработать

1. **Полная реализация модулей:**
   - Обмен валют (Index + Create)
   - Кредиты (Index + Create + Confirm + Repay)
   - Инкассация (Index + Create)
   - Курсы валют (Index + Create + Update)

2. **Статистика на Dashboard:**
   - Загрузка данных через API
   - Графики и диаграммы
   - Фильтры по периодам

3. **Система отчетов:**
   - Генерация отчетов
   - Экспорт в Excel/PDF
   - Фильтрация данных

4. **Улучшения UX:**
   - Toast уведомления
   - Модальные окна
   - Загрузчики (spinners)
   - Подтверждения действий

5. **Адаптивность:**
   - Мобильная версия
   - Responsive design
   - Touch gestures

---

## Тестирование

### Локальное тестирование

```bash
# 1. Запустите Laravel сервер
php artisan serve

# 2. В другом терминале запустите Vite dev server
npm run dev

# 3. Откройте браузер
http://localhost:8000
```

### Тестовые данные для входа

```
Администратор:
Логин: admin
Пароль: admin123

Кассир:
Логин: cashier1
Пароль: cashier123
```

---

## Troubleshooting

### Ошибка: "Module not found"

```bash
# Переустановите зависимости
rm -rf node_modules package-lock.json
npm install
```

### Ошибка: "401 Unauthorized"

Проверьте:
1. Токен в localStorage
2. Middleware auth:sanctum в routes/api.php
3. CORS настройки

### Vite не подключается

```bash
# Проверьте, что Vite запущен
npm run dev

# Очистите кэш
npm run build
php artisan optimize:clear
```

### Изменения не применяются

```bash
# Пересоберите assets
npm run build

# Очистите кэш Laravel
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

**Версия:** 1.0.0
**Дата:** 31 декабря 2025
