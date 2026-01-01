<template>
  <div class="create-exchange-page">
    <div class="page-header">
      <h1 class="page-title">Новый обмен валюты</h1>
      <router-link to="/exchanges" class="btn btn-secondary">
        ← Назад
      </router-link>
    </div>

    <div class="form-card">
      <form @submit.prevent="handleSubmit">
        <div class="form-row">
          <div class="form-group">
            <label for="type">Тип операции *</label>
            <select
              id="type"
              v-model="form.type"
              class="form-select"
              :class="{ 'error': errors.type }"
              required
            >
              <option value="">Выберите тип</option>
              <option value="buy">Покупка USD (продаем UZS)</option>
              <option value="sell">Продажа USD (покупаем UZS)</option>
            </select>
            <span v-if="errors.type" class="error-message">
              {{ errors.type[0] }}
            </span>
          </div>

          <div class="form-group">
            <label for="usd_amount">Сумма в USD *</label>
            <input
              id="usd_amount"
              v-model.number="form.usd_amount"
              type="number"
              step="0.01"
              class="form-input"
              :class="{ 'error': errors.usd_amount }"
              placeholder="0.00"
              required
            />
            <span v-if="errors.usd_amount" class="error-message">
              {{ errors.usd_amount[0] }}
            </span>
          </div>
        </div>

        <div v-if="latestRate" class="rate-info">
          <div class="info-title">Текущий курс</div>
          <div class="info-row">
            <span>Покупка USD:</span>
            <span class="info-value">{{ formatNumber(latestRate.buy_rate) }} UZS</span>
          </div>
          <div class="info-row">
            <span>Продажа USD:</span>
            <span class="info-value">{{ formatNumber(latestRate.sell_rate) }} UZS</span>
          </div>
        </div>

        <div v-if="calculatedAmount" class="calculation-info">
          <div class="info-row">
            <span>Сумма в USD:</span>
            <span class="info-value">{{ formatNumber(form.usd_amount) }} USD</span>
          </div>
          <div class="info-row">
            <span>Курс:</span>
            <span class="info-value">{{ formatNumber(currentRate) }} UZS</span>
          </div>
          <div class="info-row total">
            <span>{{ form.type === 'buy' ? 'Мы платим UZS:' : 'Мы получаем UZS:' }}</span>
            <span class="info-value">{{ formatNumber(calculatedAmount) }} UZS</span>
          </div>
        </div>

        <div v-if="submitError" class="alert alert-error">
          {{ submitError }}
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Создание...' : 'Создать обмен' }}
          </button>
          <router-link to="/exchanges" class="btn btn-secondary">
            Отмена
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();

const form = ref({
  type: '',
  usd_amount: 0
});

const latestRate = ref(null);
const errors = ref({});
const submitError = ref(null);
const loading = ref(false);

const currentRate = computed(() => {
  if (!latestRate.value || !form.value.type) return 0;
  return form.value.type === 'buy'
    ? parseFloat(latestRate.value.buy_rate)
    : parseFloat(latestRate.value.sell_rate);
});

const calculatedAmount = computed(() => {
  if (!form.value.usd_amount || !currentRate.value) return 0;
  return form.value.usd_amount * currentRate.value;
});

onMounted(async () => {
  await loadLatestRate();
});

async function loadLatestRate() {
  try {
    const response = await api.get('/rates/latest');
    if (response.data.success) {
      latestRate.value = response.data.data;
    }
  } catch (err) {
    console.error('Error loading rate:', err);
  }
}

async function handleSubmit() {
  errors.value = {};
  submitError.value = null;
  loading.value = true;

  try {
    const response = await api.post('/exchanges', form.value);

    if (response.data.success) {
      router.push({ name: 'Exchanges' });
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
    }
    submitError.value = err.response?.data?.message || 'Ошибка при создании обмена';
  } finally {
    loading.value = false;
  }
}

function formatNumber(value) {
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
}
</script>

<style scoped>
.create-exchange-page {
  max-width: 900px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 32px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
}

.form-card {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
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

.form-input,
.form-select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.2s;
  outline: none;
}

.form-input:focus,
.form-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error,
.form-select.error {
  border-color: #e74c3c;
}

.error-message {
  display: block;
  margin-top: 6px;
  color: #e74c3c;
  font-size: 13px;
}

.rate-info {
  background: #e3f2fd;
  padding: 20px;
  border-radius: 8px;
  margin: 24px 0;
  border-left: 4px solid #2196f3;
}

.info-title {
  font-weight: 600;
  font-size: 16px;
  color: #1976d2;
  margin-bottom: 12px;
}

.calculation-info {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin: 24px 0;
}

.info-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 15px;
  color: #333;
}

.info-row:last-child {
  margin-bottom: 0;
}

.info-row.total {
  border-top: 2px solid #dee2e6;
  padding-top: 12px;
  margin-top: 12px;
  font-weight: 600;
  font-size: 18px;
  color: #2c3e50;
}

.info-value {
  font-weight: 500;
}

.alert {
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.alert-error {
  background: #fee;
  border: 1px solid #fcc;
  color: #c33;
}

.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 30px;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-block;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}
</style>
