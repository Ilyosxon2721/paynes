<template>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <h1>Anesi Kassa</h1>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/" class="nav-item">
          <span>📊</span> Главная
        </router-link>

        <router-link to="/payments" class="nav-item">
          <span>💳</span> Платежи
        </router-link>

        <router-link to="/payment-types" class="nav-item" v-if="authStore.isAdmin">
          <span>📋</span> Типы платежей
        </router-link>

        <router-link to="/exchanges" class="nav-item">
          <span>💱</span> Обмен валют
        </router-link>

        <router-link to="/credits" class="nav-item">
          <span>💰</span> Кредиты
        </router-link>

        <router-link to="/incashes" class="nav-item">
          <span>💵</span> Инкассация
        </router-link>

        <router-link to="/rates" class="nav-item" v-if="authStore.isAdmin">
          <span>📈</span> Курсы валют
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <div class="user-info">
          <div class="user-name">{{ authStore.user?.full_name }}</div>
          <div class="user-position">{{ positionText }}</div>
        </div>
        <button @click="handleLogout" class="logout-btn">Выход</button>
      </div>
    </aside>

    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const positionText = computed(() => {
  return authStore.user?.position === 'admin' ? 'Администратор' : 'Кассир';
});

async function handleLogout() {
  await authStore.logout();
  router.push({ name: 'Login' });
}
</script>

<style scoped>
.layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 260px;
  background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  left: 0;
  top: 0;
}

.sidebar-header {
  padding: 24px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header h1 {
  font-size: 24px;
  font-weight: 600;
  margin: 0;
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
  background: rgba(52, 152, 219, 0.2);
  color: white;
  border-left: 3px solid #3498db;
  padding-left: 17px;
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
  margin-left: 260px;
  padding: 30px;
  background: #f5f5f5;
  min-height: 100vh;
}
</style>
