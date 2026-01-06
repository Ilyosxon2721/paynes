<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="logo-container">
          <svg class="logo-svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <!-- Simplified P logo -->
            <path d="M 50 150 L 50 50 Q 50 30 70 30 L 120 30 Q 150 30 150 60 Q 150 90 120 90 L 80 90 L 120 130 L 80 130 Z"
                  fill="#1A77C9" stroke="none"/>
          </svg>
          <h1 class="brand-name">Paynes</h1>
          <p class="brand-tagline">Комфортные платежи</p>
        </div>
        <p>Вход в систему</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="login">Логин</label>
          <input
            id="login"
            v-model="form.login"
            type="text"
            class="form-input"
            :class="{ 'error': errors.login }"
            placeholder="Введите логин"
            required
          />
          <span v-if="errors.login" class="error-message">{{ errors.login }}</span>
        </div>

        <div class="form-group">
          <label for="password">Пароль</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="form-input"
            :class="{ 'error': errors.password }"
            placeholder="Введите пароль"
            required
          />
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>

        <div v-if="authStore.error" class="alert alert-error">
          {{ authStore.error }}
        </div>

        <button
          type="submit"
          class="btn btn-primary"
          :disabled="authStore.loading"
        >
          {{ authStore.loading ? 'Вход...' : 'Войти' }}
        </button>
      </form>

      <div class="login-footer">
        <p class="test-credentials">
          <strong>Тестовые данные:</strong><br>
          Админ: admin / admin123<br>
          Кассир: cashier1 / cashier123
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  login: '',
  password: ''
});

const errors = ref({});

async function handleLogin() {
  errors.value = {};

  // Basic validation
  if (!form.value.login) {
    errors.value.login = 'Логин обязателен';
    return;
  }

  if (!form.value.password) {
    errors.value.password = 'Пароль обязателен';
    return;
  }

  try {
    await authStore.login(form.value);

    // Redirect based on user role
    if (authStore.user?.position === 'cashier') {
      router.push({ name: 'CashierDashboard' });
    } else {
      router.push({ name: 'Dashboard' });
    }
  } catch (err) {
    console.error('Login error:', err);
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--paynes-gradient-primary);
  padding: 20px;
}

.login-card {
  background: white;
  border-radius: 16px;
  box-shadow: var(--paynes-shadow-xl);
  width: 100%;
  max-width: 420px;
  overflow: hidden;
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.login-header {
  background: white;
  color: var(--paynes-blue-dark);
  padding: 40px 30px 30px;
  text-align: center;
  border-bottom: 3px solid var(--paynes-blue-primary);
}

.login-header .logo-container {
  margin-bottom: 20px;
}

.login-header .logo-svg {
  width: 80px;
  height: 80px;
  margin: 0 auto 12px;
  display: block;
}

.login-header .brand-name {
  font-size: 36px;
  font-weight: 700;
  color: var(--paynes-blue-primary);
  margin: 0 0 4px 0;
  letter-spacing: 1px;
}

.login-header .brand-tagline {
  font-size: 14px;
  color: var(--paynes-gray-600);
  margin: 0 0 16px 0;
}

.login-header p {
  margin: 0;
  font-size: 16px;
  font-weight: 500;
  color: var(--paynes-gray-600);
}

.login-form {
  padding: 30px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #333;
  font-size: 14px;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.3s;
  outline: none;
}

.form-input:focus {
  border-color: var(--paynes-blue-primary);
  box-shadow: 0 0 0 3px rgba(26, 119, 201, 0.1);
}

.form-input.error {
  border-color: #e74c3c;
}

.error-message {
  display: block;
  margin-top: 6px;
  color: #e74c3c;
  font-size: 13px;
}

.alert {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 14px;
}

.alert-error {
  background: #fee;
  border: 1px solid #fcc;
  color: #c33;
}

.btn {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: var(--paynes-gradient-primary);
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(26, 119, 201, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.login-footer {
  background: #f8f9fa;
  padding: 20px 30px;
  border-top: 1px solid #e0e0e0;
}

.test-credentials {
  margin: 0;
  font-size: 13px;
  color: #666;
  line-height: 1.6;
}

.test-credentials strong {
  color: #333;
}
</style>
