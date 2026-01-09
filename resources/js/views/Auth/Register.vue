<template>
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <router-link to="/" class="back-link">← Назад</router-link>
        <div class="logo-container">
          <span class="logo-icon">💳</span>
          <h1 class="brand-name">Paynes</h1>
          <p class="brand-tagline">Комфортные платежи</p>
        </div>
        <p>Регистрация агента</p>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <!-- Тариф -->
        <div class="form-group">
          <label>Выберите тариф *</label>
          <div class="tariff-selector">
            <label v-for="tariff in tariffs" :key="tariff.id" 
                   class="tariff-option" :class="{ selected: form.tariff === tariff.id, popular: tariff.popular }">
              <input type="radio" v-model="form.tariff" :value="tariff.id" required />
              <div class="tariff-content">
                <span v-if="tariff.popular" class="popular-badge">⭐</span>
                <div class="tariff-name">{{ tariff.name }}</div>
                <div class="tariff-price">{{ tariff.price }}</div>
                <div class="tariff-features">{{ tariff.features }}</div>
              </div>
            </label>
          </div>
          <span v-if="errors.tariff" class="error-message">{{ errors.tariff }}</span>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="name">Имя *</label>
            <input id="name" v-model="form.name" type="text" class="form-input" 
                   :class="{ 'error': errors.name }" placeholder="Ваше имя" required />
            <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
          </div>
          <div class="form-group">
            <label for="login">Логин *</label>
            <input id="login" v-model="form.login" type="text" class="form-input" 
                   :class="{ 'error': errors.login }" placeholder="Придумайте логин" required />
            <span v-if="errors.login" class="error-message">{{ errors.login }}</span>
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email *</label>
          <input id="email" v-model="form.email" type="email" class="form-input" 
                 :class="{ 'error': errors.email }" placeholder="email@example.com" required />
          <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
        </div>

        <div class="form-group">
          <label for="phone">Телефон</label>
          <input id="phone" v-model="form.phone" type="tel" class="form-input" 
                 placeholder="+998 90 123 45 67" />
        </div>

        <div class="form-group">
          <label for="branch">Название точки / Филиал</label>
          <input id="branch" v-model="form.branch" type="text" class="form-input" 
                 placeholder="Например: Чиланзар-5" />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Пароль *</label>
            <input id="password" v-model="form.password" type="password" class="form-input" 
                   :class="{ 'error': errors.password }" placeholder="Минимум 6 символов" required />
            <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
          </div>
          <div class="form-group">
            <label for="password_confirmation">Подтверждение *</label>
            <input id="password_confirmation" v-model="form.password_confirmation" type="password" 
                   class="form-input" :class="{ 'error': errors.password_confirmation }" 
                   placeholder="Повторите пароль" required />
            <span v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation }}</span>
          </div>
        </div>

        <div v-if="serverError" class="alert alert-error">{{ serverError }}</div>

        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? 'Регистрация...' : 'Зарегистрироваться' }}
        </button>

        <p class="login-link">
          Уже есть аккаунт? <router-link to="/login">Войти</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const tariffs = [
  { id: 'starter', name: 'Стартовый', price: '299 000 UZS/мес', features: '1 кассир, 1 филиал', popular: false },
  { id: 'business', name: 'Бизнес', price: '599 000 UZS/мес', features: '5 кассиров, 1 менеджер, 3 филиала', popular: true },
  { id: 'enterprise', name: 'Корпоративный', price: '1 199 000 UZS/мес', features: '15 кассиров, 3 менеджера, 10 филиалов', popular: false }
];

const form = ref({
  tariff: 'business',
  name: '',
  login: '',
  email: '',
  phone: '',
  branch: '',
  password: '',
  password_confirmation: ''
});

const errors = ref({});
const serverError = ref('');
const loading = ref(false);

async function handleRegister() {
  errors.value = {};
  serverError.value = '';

  // Validation
  if (!form.value.tariff) { errors.value.tariff = 'Выберите тариф'; return; }
  if (!form.value.name) { errors.value.name = 'Имя обязательно'; return; }
  if (!form.value.login) { errors.value.login = 'Логин обязателен'; return; }
  if (!form.value.email) { errors.value.email = 'Email обязателен'; return; }
  if (!form.value.password || form.value.password.length < 6) { 
    errors.value.password = 'Пароль минимум 6 символов'; return; 
  }
  if (form.value.password !== form.value.password_confirmation) { 
    errors.value.password_confirmation = 'Пароли не совпадают'; return; 
  }

  loading.value = true;

  try {
    await axios.post('/api/register', form.value);
    router.push({ name: 'ThankYou' });
  } catch (err) {
    if (err.response?.data?.errors) {
      const serverErrors = err.response.data.errors;
      Object.keys(serverErrors).forEach(key => {
        errors.value[key] = serverErrors[key][0];
      });
    } else {
      serverError.value = err.response?.data?.message || 'Ошибка регистрации. Попробуйте позже.';
    }
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.register-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0,0,0,0.25);
  width: 100%;
  max-width: 560px;
  overflow: hidden;
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.register-header {
  background: white;
  color: #333;
  padding: 30px;
  text-align: center;
  border-bottom: 3px solid #667eea;
  position: relative;
}

.back-link {
  position: absolute;
  left: 20px;
  top: 20px;
  color: #667eea;
  text-decoration: none;
  font-size: 14px;
}

.back-link:hover { text-decoration: underline; }

.logo-container { margin-bottom: 15px; }
.logo-icon { font-size: 48px; display: block; margin-bottom: 10px; }
.brand-name { font-size: 28px; font-weight: 700; color: #667eea; margin: 0; }
.brand-tagline { font-size: 13px; color: #666; margin: 5px 0 0; }
.register-header > p { margin: 0; font-size: 15px; font-weight: 500; color: #666; }

.register-form { padding: 25px 30px 30px; }

/* Tariff Selector */
.tariff-selector {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

.tariff-option {
  position: relative;
  cursor: pointer;
}

.tariff-option input {
  position: absolute;
  opacity: 0;
}

.tariff-content {
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  padding: 12px 10px;
  text-align: center;
  transition: all 0.3s;
  background: #fafafa;
}

.tariff-option:hover .tariff-content {
  border-color: #667eea;
}

.tariff-option.selected .tariff-content {
  border-color: #667eea;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
}

.tariff-option.popular .tariff-content {
  border-color: #667eea;
}

.popular-badge {
  position: absolute;
  top: -8px;
  right: 5px;
  font-size: 14px;
}

.tariff-name {
  font-weight: 600;
  font-size: 13px;
  color: #333;
  margin-bottom: 4px;
}

.tariff-price {
  font-size: 11px;
  color: #667eea;
  font-weight: 600;
  margin-bottom: 4px;
}

.tariff-features {
  font-size: 10px;
  color: #888;
  line-height: 1.3;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

.form-group { margin-bottom: 18px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #333; font-size: 13px; }

.form-input {
  width: 100%;
  padding: 11px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s;
  outline: none;
}

.form-input:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
.form-input.error { border-color: #e74c3c; }
.error-message { display: block; margin-top: 4px; color: #e74c3c; font-size: 12px; }

.alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; }
.alert-error { background: #fee; border: 1px solid #fcc; color: #c33; }

.btn {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.login-link { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
.login-link a { color: #667eea; text-decoration: none; font-weight: 500; }
.login-link a:hover { text-decoration: underline; }

@media (max-width: 500px) {
  .form-row { grid-template-columns: 1fr; }
  .tariff-selector { grid-template-columns: 1fr; }
}
</style>
