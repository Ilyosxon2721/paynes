<template>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <svg class="logo" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <path d="M 50 150 L 50 50 Q 50 30 70 30 L 120 30 Q 150 30 150 60 Q 150 90 120 90 L 80 90 L 120 130 L 80 130 Z"
                fill="white" stroke="none"/>
        </svg>
        <h1>Paynes</h1>
        <p class="tagline">Платежная система</p>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/cashier" class="nav-item">
          <span>🏠</span> Главная
        </router-link>

        <router-link to="/cashier/payments" class="nav-item">
          <span>💳</span> Платежи
        </router-link>

        <router-link to="/cashier/exchanges" class="nav-item">
          <span>💱</span> Обмен валют
        </router-link>

        <router-link to="/cashier/credits" class="nav-item">
          <span>💰</span> Кредиты
        </router-link>

        <router-link to="/cashier/incashes" class="nav-item">
          <span>🧰</span> Инкассация
        </router-link>

        <router-link to="/cashier/reports" class="nav-item">
          <span>📊</span> Отчеты
        </router-link>

        <router-link to="/cashier/tickets" class="nav-item">
          <span>💬</span> Чат/Тикеты
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <div class="user-info">
          <div class="user-name">{{ authStore.user?.full_name }}</div>
          <div class="user-position">Кассир</div>
        </div>
        <button @click="handleLogout" class="logout-btn">Выйти</button>
      </div>
    </aside>

    <main class="main-content">
      <!-- Shift Controls at the top -->
      <div class="shift-controls-container">
        <ShiftControls
          @shift-opened="handleShiftOpened"
          @shift-closed="handleShiftClosed"
          @shift-changed="handleShiftChanged"
        />
      </div>

      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import ShiftControls from '@/components/ShiftControls.vue';

const router = useRouter();
const authStore = useAuthStore();
const currentShift = ref(null);

async function handleLogout() {
  await authStore.logout();
  router.push({ name: 'Login' });
}

function handleShiftOpened(shift) {
  currentShift.value = shift;
  // You can show a success message here if needed
  console.log('Смена успешно открыта', shift);
}

function handleShiftClosed(shift) {
  currentShift.value = null;
  console.log('Смена успешно закрыта', shift);
}

function handleShiftChanged(shift) {
  currentShift.value = shift;
}
</script>

<style scoped>
.layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 280px;
  background: var(--paynes-gradient-primary);
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  left: 0;
  top: 0;
  box-shadow: var(--paynes-shadow-lg);
}

.sidebar-header {
  padding: 24px 20px;
  border-bottom: 2px solid rgba(255, 255, 255, 0.2);
  text-align: center;
}

.sidebar-header .logo {
  width: 56px;
  height: 56px;
  margin: 0 auto 12px;
  display: block;
}

.sidebar-header h1 {
  font-size: 26px;
  font-weight: 700;
  margin: 0 0 4px 0;
  letter-spacing: 0.5px;
}

.sidebar-header .tagline {
  font-size: 12px;
  margin: 0;
  opacity: 0.85;
  font-weight: 400;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: all 0.2s;
  gap: 12px;
  font-size: 15px;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.nav-item.router-link-active {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  border-left: 4px solid white;
  padding-left: 16px;
  font-weight: 600;
}

.nav-item span {
  font-size: 18px;
}

.sidebar-footer {
  padding: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info {
  margin-bottom: 12px;
}

.user-name {
  font-weight: 500;
  font-size: 14px;
  margin-bottom: 4px;
}

.user-position {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
}

.logout-btn {
  width: 100%;
  padding: 10px;
  background: rgba(231, 76, 60, 0.2);
  border: 1px solid rgba(231, 76, 60, 0.5);
  color: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.logout-btn:hover {
  background: rgba(231, 76, 60, 0.3);
  border-color: #e74c3c;
}

.main-content {
  flex: 1;
  margin-left: 280px;
  padding: 30px;
  background: var(--paynes-gray-50);
  min-height: 100vh;
}

.shift-controls-container {
  margin-bottom: 24px;
}

.shift-controls-container :deep(.shift-controls) {
  margin-bottom: 0;
}
</style>
