<template>
  <div class="exchanges-page">
    <div class="page-header">
      <h1 class="page-title">Обмен валют</h1>
      <router-link to="/exchanges/create" class="btn btn-primary">
        + Новый обмен
      </router-link>
    </div>

    <div class="filters-card">
      <div class="filters">
        <div class="filter-group">
          <label>Тип операции</label>
          <select v-model="filters.type" @change="loadExchanges" class="form-select">
            <option value="">Все</option>
            <option value="buy">Покупка</option>
            <option value="sell">Продажа</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Дата</label>
          <input
            type="date"
            v-model="filters.date"
            @change="loadExchanges"
            class="form-input"
          />
        </div>

        <button @click="clearFilters" class="btn btn-secondary">Очистить</button>
      </div>
    </div>

    <div v-if="loading" class="loading">Загрузка...</div>

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else-if="exchanges.length === 0" class="empty-state">
      <p>Обмены валют не найдены</p>
    </div>

    <div v-else class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Дата/Время</th>
            <th>Тип операции</th>
            <th>Сумма USD</th>
            <th>Курс</th>
            <th>Сумма UZS</th>
            <th>Кассир</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="exchange in exchanges" :key="exchange.id">
            <td>
              <div>{{ exchange.date }}</div>
              <div class="text-small">{{ exchange.time }}</div>
            </td>
            <td>
              <span class="badge" :class="exchange.type === 'buy' ? 'badge-success' : 'badge-info'">
                {{ exchange.type === 'buy' ? 'Покупка' : 'Продажа' }}
              </span>
            </td>
            <td class="font-weight-bold">{{ formatNumber(exchange.usd_amount) }} USD</td>
            <td>{{ formatNumber(exchange.rate) }}</td>
            <td>{{ formatNumber(exchange.uzs_amount) }} UZS</td>
            <td>{{ exchange.cashier?.full_name }}</td>
            <td>
              <div class="action-buttons">
                <button
                  @click="deleteExchange(exchange.id)"
                  class="btn-action btn-danger"
                  title="Удалить"
                >
                  ×
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="meta" class="pagination">
        <button
          @click="changePage(meta.current_page - 1)"
          :disabled="meta.current_page === 1"
          class="btn btn-secondary"
        >
          Назад
        </button>
        <span class="pagination-info">
          Страница {{ meta.current_page }} из {{ meta.last_page }}
        </span>
        <button
          @click="changePage(meta.current_page + 1)"
          :disabled="meta.current_page === meta.last_page"
          class="btn btn-secondary"
        >
          Вперед
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const exchanges = ref([]);
const meta = ref(null);
const loading = ref(false);
const error = ref(null);
const filters = ref({
  type: '',
  date: '',
  page: 1
});

onMounted(() => {
  loadExchanges();
});

async function loadExchanges() {
  loading.value = true;
  error.value = null;

  try {
    const params = new URLSearchParams();
    if (filters.value.type) params.append('type', filters.value.type);
    if (filters.value.date) params.append('date', filters.value.date);
    params.append('page', filters.value.page);

    const response = await api.get(`/exchanges?${params}`);

    if (response.data.success) {
      exchanges.value = response.data.data.data || response.data.data;
      meta.value = response.data.data.meta;
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка загрузки обменов';
  } finally {
    loading.value = false;
  }
}

function clearFilters() {
  filters.value = { type: '', date: '', page: 1 };
  loadExchanges();
}

function changePage(page) {
  filters.value.page = page;
  loadExchanges();
}

async function deleteExchange(id) {
  if (!confirm('Удалить обмен валюты?')) return;

  try {
    await api.delete(`/exchanges/${id}`);
    await loadExchanges();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка удаления обмена');
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
.exchanges-page {
  max-width: 1600px;
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

.filters-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.filters {
  display: flex;
  gap: 16px;
  align-items: flex-end;
}

.filter-group {
  flex: 1;
  max-width: 250px;
}

.filter-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  font-size: 14px;
  color: #333;
}

.form-select,
.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: all 0.2s;
}

.form-select:focus,
.form-input:focus {
  border-color: #667eea;
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

.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.empty-state p {
  color: #666;
  font-size: 16px;
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

.text-small {
  font-size: 12px;
  color: #666;
  margin-top: 2px;
}

.font-weight-bold {
  font-weight: 600;
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-success {
  background: #d4edda;
  color: #155724;
}

.badge-info {
  background: #d1ecf1;
  color: #0c5460;
}

.action-buttons {
  display: flex;
  gap: 6px;
}

.btn-action {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.2s;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover {
  background: #c82333;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
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

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #5a6268;
}

.btn-secondary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-top: 1px solid #e0e0e0;
}

.pagination-info {
  font-size: 14px;
  color: #666;
}
</style>
