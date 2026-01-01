<template>
  <div class="payment-types-page">
    <div class="page-header">
      <h1 class="page-title">Типы платежей</h1>
    </div>

    <div v-if="loading" class="loading">Загрузка...</div>

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Название</th>
            <th>Организация</th>
            <th>Банк</th>
            <th>Счет</th>
            <th>МФО</th>
            <th>ИНН</th>
            <th>Комиссия %</th>
            <th>Комиссия фикс.</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="type in paymentTypes" :key="type.id">
            <td>{{ type.name }}</td>
            <td>{{ type.organization }}</td>
            <td>{{ type.bank }}</td>
            <td>{{ type.account_number }}</td>
            <td>{{ type.mfo }}</td>
            <td>{{ type.inn }}</td>
            <td>{{ type.commission_percentage }}%</td>
            <td>{{ formatNumber(type.commission_fixed) }} UZS</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const paymentTypes = ref([]);
const loading = ref(false);
const error = ref(null);

onMounted(() => {
  loadPaymentTypes();
});

async function loadPaymentTypes() {
  loading.value = true;
  error.value = null;

  try {
    const response = await api.get('/payment-types');

    if (response.data.success) {
      paymentTypes.value = response.data.data;
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка загрузки типов платежей';
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
.payment-types-page {
  max-width: 1600px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 24px;
}

.page-title {
  font-size: 32px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
}

.loading {
  text-align: center;
  padding: 40px;
  font-size: 18px;
  color: #666;
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

.table-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #f8f9fa;
  padding: 16px 12px;
  text-align: left;
  font-weight: 600;
  font-size: 13px;
  color: #2c3e50;
  border-bottom: 2px solid #e0e0e0;
}

.data-table td {
  padding: 14px 12px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  color: #333;
}

.data-table tr:hover {
  background: #f8f9fa;
}
</style>
