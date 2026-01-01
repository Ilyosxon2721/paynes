<template>
  <div class="dashboard">
    <h1 class="page-title">Главная панель</h1>

    <div class="welcome-card">
      <h2>Добро пожаловать, {{ authStore.user?.full_name }}!</h2>
      <p>{{ positionText }} - {{ authStore.user?.branch || 'Главный офис' }}</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">💳</div>
        <div class="stat-info">
          <div class="stat-label">Платежи</div>
          <div class="stat-value">-</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">💱</div>
        <div class="stat-info">
          <div class="stat-label">Обмен валют</div>
          <div class="stat-value">-</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
          <div class="stat-label">Кредиты</div>
          <div class="stat-value">-</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">💵</div>
        <div class="stat-info">
          <div class="stat-label">Инкассация</div>
          <div class="stat-value">-</div>
        </div>
      </div>
    </div>

    <div class="quick-actions">
      <h3>Быстрые действия</h3>
      <div class="action-buttons">
        <router-link to="/payments/create" class="action-btn">
          <span>💳</span>
          <span>Новый платеж</span>
        </router-link>

        <router-link to="/exchanges/create" class="action-btn">
          <span>💱</span>
          <span>Обмен валюты</span>
        </router-link>

        <router-link to="/credits/create" class="action-btn">
          <span>💰</span>
          <span>Новый кредит</span>
        </router-link>

        <router-link to="/incashes/create" class="action-btn">
          <span>💵</span>
          <span>Инкассация</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const positionText = computed(() => {
  return authStore.user?.position === 'admin' ? 'Администратор' : 'Кассир';
});
</script>

<style scoped>
.dashboard {
  max-width: 1400px;
  margin: 0 auto;
}

.page-title {
  font-size: 32px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0 0 30px 0;
}

.welcome-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px;
  border-radius: 12px;
  margin-bottom: 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.welcome-card h2 {
  margin: 0 0 8px 0;
  font-size: 24px;
  font-weight: 600;
}

.welcome-card p {
  margin: 0;
  opacity: 0.9;
  font-size: 16px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  font-size: 48px;
}

.stat-info {
  flex: 1;
}

.stat-label {
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 28px;
  font-weight: 600;
  color: #2c3e50;
}

.quick-actions {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.quick-actions h3 {
  margin: 0 0 20px 0;
  font-size: 20px;
  font-weight: 600;
  color: #2c3e50;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 24px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 10px;
  text-decoration: none;
  color: #2c3e50;
  font-weight: 500;
  transition: all 0.3s;
}

.action-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.action-btn span:first-child {
  font-size: 36px;
}

.action-btn span:last-child {
  font-size: 15px;
}
</style>
