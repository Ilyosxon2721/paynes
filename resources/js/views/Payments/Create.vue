<template>
  <div class="create-payment-page">
    <div class="page-header">
      <h1 class="page-title">Новый платеж</h1>
      <router-link to="/payments" class="btn btn-secondary">
        ← Назад
      </router-link>
    </div>

    <div class="form-card">
      <form @submit.prevent="handleSubmit">
        <div class="form-row">
          <div class="form-group">
            <label for="payment_type_id">Тип платежа *</label>
            <select
              id="payment_type_id"
              v-model="form.payment_type_id"
              class="form-select"
              :class="{ 'error': errors.payment_type_id }"
              required
            >
              <option value="">Выберите тип платежа</option>
              <option v-for="type in paymentTypes" :key="type.id" :value="type.id">
                {{ type.name }} ({{ type.commission_percentage }}%)
              </option>
            </select>
            <span v-if="errors.payment_type_id" class="error-message">
              {{ errors.payment_type_id[0] }}
            </span>
          </div>

          <div class="form-group">
            <label for="list_number">Номер списка</label>
            <input
              id="list_number"
              v-model="form.list_number"
              type="text"
              class="form-input"
              placeholder="Номер списка"
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="payer_name">Плательщик *</label>
            <input
              id="payer_name"
              v-model="form.payer_name"
              type="text"
              class="form-input"
              :class="{ 'error': errors.payer_name }"
              placeholder="ФИО плательщика"
              required
            />
            <span v-if="errors.payer_name" class="error-message">
              {{ errors.payer_name[0] }}
            </span>
          </div>

          <div class="form-group">
            <label for="amount">Сумма *</label>
            <input
              id="amount"
              v-model.number="form.amount"
              type="number"
              step="0.01"
              class="form-input"
              :class="{ 'error': errors.amount }"
              placeholder="0.00"
              required
            />
            <span v-if="errors.amount" class="error-message">
              {{ errors.amount[0] }}
            </span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="payment_method">Способ оплаты *</label>
            <select
              id="payment_method"
              v-model="form.payment_method"
              class="form-select"
              required
            >
              <option value="cash">Наличные</option>
              <option value="card">Карта</option>
            </select>
          </div>

          <div class="form-group">
            <label for="currency">Валюта *</label>
            <select
              id="currency"
              v-model="form.currency"
              class="form-select"
              required
            >
              <option value="UZS">UZS</option>
              <option value="USD">USD</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="purpose">Назначение платежа</label>
          <textarea
            id="purpose"
            v-model="form.purpose"
            class="form-textarea"
            rows="3"
            placeholder="Введите назначение платежа"
          ></textarea>
        </div>

        <div v-if="calculatedCommission" class="commission-info">
          <div class="info-row">
            <span>Сумма:</span>
            <span class="info-value">{{ formatNumber(form.amount) }} {{ form.currency }}</span>
          </div>
          <div class="info-row">
            <span>Комиссия:</span>
            <span class="info-value">{{ formatNumber(calculatedCommission) }} {{ form.currency }}</span>
          </div>
          <div class="info-row total">
            <span>Итого:</span>
            <span class="info-value">{{ formatNumber(form.amount + calculatedCommission) }} {{ form.currency }}</span>
          </div>
        </div>

        <div v-if="submitError" class="alert alert-error">
          {{ submitError }}
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Создание...' : 'Создать платеж' }}
          </button>
          <router-link to="/payments" class="btn btn-secondary">
            Отмена
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();

const form = ref({
  payment_type_id: '',
  payer_name: '',
  purpose: '',
  amount: 0,
  payment_method: 'cash',
  currency: 'UZS',
  list_number: ''
});

const paymentTypes = ref([]);
const errors = ref({});
const submitError = ref(null);
const loading = ref(false);

const selectedPaymentType = computed(() => {
  return paymentTypes.value.find(t => t.id === form.value.payment_type_id);
});

const calculatedCommission = computed(() => {
  if (!selectedPaymentType.value || !form.value.amount) return 0;

  const percentCommission = form.value.amount * (selectedPaymentType.value.commission_percentage / 100);
  return Math.max(percentCommission, parseFloat(selectedPaymentType.value.commission_fixed || 0));
});

onMounted(async () => {
  await loadPaymentTypes();
});

async function loadPaymentTypes() {
  try {
    const response = await api.get('/payment-types');
    if (response.data.success) {
      paymentTypes.value = response.data.data;
    }
  } catch (err) {
    console.error('Error loading payment types:', err);
  }
}

async function handleSubmit() {
  errors.value = {};
  submitError.value = null;
  loading.value = true;

  try {
    const response = await api.post('/payments', form.value);

    if (response.data.success) {
      router.push({ name: 'Payments' });
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
    }
    submitError.value = err.response?.data?.message || 'Ошибка при создании платежа';
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
.create-payment-page {
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
.form-select,
.form-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.2s;
  outline: none;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
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

.form-textarea {
  resize: vertical;
  font-family: inherit;
}

.commission-info {
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
