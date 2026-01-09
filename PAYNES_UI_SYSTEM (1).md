
# PAYNES UI SYSTEM — FULL SPEC (Antigravity / Codex Ready)

Версия: v1.0  
Проект: **Paynes**  
Тип: **B2B SaaS**  
Назначение: система учёта платежей, касс и смен (НЕ платёжная система)

---

## 0. Кратко о продукте

Paynes — это операционная система контроля денег:
- платежи
- кассы
- кассиры
- смены
- расхождения

Принципы:
- read-only для денег
- всё логируется
- минимум визуального шума
- контроль важнее «красоты»

---

## 1. Технологический стек

### Frontend
- Vue.js 3 (SPA)
- Vite
- Vue Router
- Pinia
- Axios

### Styling
- Vanilla CSS
- CSS Variables (design tokens)
- Scoped styles в Vue
- Глобальные UI-компоненты (design system)

---

## 2. Архитектура проекта

```
resources/
  css/
    app.css
    paynes-theme.css
    paynes-components.css
  js/
    app.js
    api/
      http.js
    router/
      index.js
    stores/
      auth.js
      ui.js
    components/
      ui/
        PButton.vue
        PInput.vue
        PCard.vue
        PModal.vue
        PStatusDot.vue
      layout/
        App.vue
        AppShell.vue
        Sidebar.vue
        Topbar.vue
    pages/
      Dashboard.vue
      Payments.vue
      Shifts.vue (planned)
      Reports.vue (planned)
      Settings.vue (planned)
```

---

## 3. Design Tokens (CSS Variables)

Файл: `paynes-theme.css`

```css
:root {
  --paynes-primary: 31 111 214;
  --paynes-primary-hover: 26 95 204;

  --paynes-bg: 245 247 250;
  --paynes-card: 255 255 255;

  --paynes-text: 11 42 74;
  --paynes-muted: 107 114 128;

  --paynes-border: 229 231 235;

  --paynes-success: 22 163 74;
  --paynes-warning: 245 158 11;
  --paynes-error: 220 38 38;
  --paynes-info: 37 99 235;

  --paynes-radius-sm: 6px;
  --paynes-radius-md: 8px;
  --paynes-radius-lg: 10px;

  --paynes-shadow-card: 0 1px 2px rgba(0,0,0,.04);
}

html.dark {
  --paynes-bg: 15 23 42;
  --paynes-card: 17 24 39;
  --paynes-text: 229 231 235;
  --paynes-muted: 156 163 175;
  --paynes-border: 31 41 55;
  --paynes-shadow-card: 0 1px 2px rgba(0,0,0,.25);
}

:root {
  --c-primary: rgb(var(--paynes-primary));
  --c-primary-hover: rgb(var(--paynes-primary-hover));
  --c-bg: rgb(var(--paynes-bg));
  --c-card: rgb(var(--paynes-card));
  --c-text: rgb(var(--paynes-text));
  --c-muted: rgb(var(--paynes-muted));
  --c-border: rgb(var(--paynes-border));
  --c-success: rgb(var(--paynes-success));
  --c-warning: rgb(var(--paynes-warning));
  --c-error: rgb(var(--paynes-error));
  --c-info: rgb(var(--paynes-info));
}
```

---

## 4. Глобальные UI-компоненты (CSS)

Файл: `paynes-components.css`

```css
body {
  font-family: Inter, system-ui, sans-serif;
  background: var(--c-bg);
  color: var(--c-text);
  margin: 0;
}

/* Card */
.p-card {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 16px;
  box-shadow: var(--paynes-shadow-card);
}
.p-card__header { padding: 18px 20px 12px; }
.p-card__title { font-size: 16px; font-weight: 700; }
.p-card__body { padding: 0 20px 20px; }

/* Button */
.p-btn {
  height: 40px;
  padding: 0 14px;
  border-radius: var(--paynes-radius-lg);
  border: none;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}
.p-btn--primary { background: var(--c-primary); color:#fff; }
.p-btn--secondary { background: var(--c-card); border:1px solid var(--c-border); }
.p-btn--danger { background: var(--c-error); color:#fff; }
.p-btn--ghost { background: transparent; }

/* Input */
.p-input {
  height: 40px;
  padding: 0 12px;
  border-radius: var(--paynes-radius-md);
  border: 1px solid var(--c-border);
  background: var(--c-card);
}

/* Table */
.p-table-wrap {
  border:1px solid var(--c-border);
  border-radius:16px;
  overflow:auto;
}
.p-table {
  width:100%;
  border-collapse:collapse;
}
.p-table th, .p-table td {
  padding:12px 14px;
  border-bottom:1px solid var(--c-border);
}
```

---

## 5. Vue UI Components (API стандарт)

### PButton.vue

```vue
<template>
  <button class="p-btn" :class="`p-btn--${variant}`" @click="$emit('click')">
    <slot />
  </button>
</template>

<script setup>
defineProps({ variant: { type: String, default: "primary" } });
defineEmits(["click"]);
</script>
```

### PInput.vue

```vue
<template>
  <div>
    <label v-if="label">{{ label }}</label>
    <input class="p-input" :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)" />
  </div>
</template>

<script setup>
defineProps({ modelValue:String, label:String });
defineEmits(["update:modelValue"]);
</script>
```

### PCard.vue

```vue
<template>
  <div class="p-card">
    <div v-if="title" class="p-card__header">
      <div class="p-card__title">{{ title }}</div>
    </div>
    <div class="p-card__body"><slot /></div>
  </div>
</template>

<script setup>
defineProps({ title:String });
</script>
```

### PStatusDot.vue

```vue
<template>
  <span>● <slot /></span>
</template>
```

---

## 6. Layout System

### AppShell
- Sidebar слева
- Topbar сверху
- Контент через `<router-view />`

Sidebar:
- Dashboard
- Payments
- Shifts (disabled на MVP)
- Reports (disabled)
- Settings (disabled)

---

## 7. Router

```js
import { createRouter, createWebHistory } from "vue-router";
import AppShell from "@/components/layout/AppShell.vue";
import Dashboard from "@/pages/Dashboard.vue";
import Payments from "@/pages/Payments.vue";

export const router = createRouter({
  history: createWebHistory(),
  routes: [{
    path: "/",
    component: AppShell,
    children: [
      { path: "", name:"dashboard", component: Dashboard },
      { path: "payments", name:"payments", component: Payments },
    ]
  }]
});
```

---

## 8. Dashboard (логика)

Содержит:
- 4 KPI cards
- 1 график (stub)
- блок «Требует внимания»
- таблицу последних платежей

Ограничения:
- 1 график
- ≤ 2 экранов по высоте
- только ключевые цифры

---

## 9. Payments Page (эталон таблицы)

Колонки:
- Дата/Время
- Касса
- Кассир
- Тип
- Сумма
- Статус
- Действие (Просмотр)

Правила:
- платежи immutable
- только read-only
- server-side pagination

---

## 10. State Management (Pinia)

### UI Store
- dark / light theme
- sidebar collapsed

### Auth Store
- user
- role
- permissions

---

## 11. UX Принципы Paynes

- Минимум цветов
- Без «финансовых» украшений
- Контроль > эмоции
- Всё, что можно сломать — запрещено

---

## 12. Roadmap UI

1. Shifts page (open / close shift modal)
2. Reports pages
3. Settings
4. API integration
5. Mobile responsive pass

---

## Итог

Этот файл — **единый источник правды для Paynes UI**.

Использовать:
- как промпт для Antigravity
- как документацию для команды
- как основу для генерации кода

