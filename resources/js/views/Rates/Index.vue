<template>
  <div class="rates-page">
    <div class="page-header">
      <h1 class="page-title">Курсы валют</h1>
      <button @click="showCreateForm = true" class="btn btn-primary">+ Новый курс</button>
    </div>

    <!-- Форма создания/редактирования -->
    <div v-if="showCreateForm" class="form-modal">
      <div class="form-card">
        <div class="form-header">
          <h2>{{ editingRate ? 'Редактировать курс' : 'Новый курс' }}</h2>
          <button @click="cancelForm" class="btn-close">×</button>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="form-row">
            <div class="form-group">
              <label for="buy_rate">Курс покупки *</label>
              <input id="buy_rate" v-model.number="form.buy_rate" type="number" step="0.01" class="form-input" placeholder="12500.00" required />
            </div>
            <div class="form-group">
              <label for="sell_rate">Курс продажи *</label>
              <input id="sell_rate" v-model.number="form.sell_rate" type="number" step="0.01" class="form-input" placeholder="12600.00" required />
            </div>
            <div class="form-group">
              <label for="date">Дата *</label>
              <input id="date" v-model="form.date" type="date" class="form-input" required />
            </div>
          </div>
          <div v-if="submitError" class="alert alert-error">{{ submitError }}</div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Сохранение...' : 'Сохранить' }}
            </button>
            <button type="button" @click="cancelForm" class="btn btn-secondary">Отмена</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="loadingRates" class="loading">Загрузка...</div>
    <div v-else-if="rates.length === 0" class="empty-state"><p>Курсы валют не найдены</p></div>
    <div v-else class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Дата</th>
            <th>Курс покупки USD</th>
            <th>Курс продажи USD</th>
            <th>Создано</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rate in rates" :key="rate.id">
            <td class="font-weight-bold">{{ rate.date }}</td>
            <td>{{ formatNumber(rate.buy_rate) }} UZS</td>
            <td>{{ formatNumber(rate.sell_rate) }} UZS</td>
            <td class="text-small">{{ rate.created_at }}</td>
            <td>
              <div class="action-buttons">
                <button @click="editRate(rate)" class="btn-action btn-info" title="Редактировать">✎</button>
                <button @click="deleteRate(rate.id)" class="btn-action btn-danger" title="Удалить">×</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const rates = ref([]);
const loadingRates = ref(false);
const showCreateForm = ref(false);
const editingRate = ref(null);
const form = ref({ buy_rate: 0, sell_rate: 0, date: new Date().toISOString().split('T')[0] });
const submitError = ref(null);
const loading = ref(false);

onMounted(() => loadRates());

async function loadRates() {
  loadingRates.value = true;
  try {
    const response = await api.get('/rates');
    if (response.data.success) {
      rates.value = response.data.data;
    }
  } finally {
    loadingRates.value = false;
  }
}

function editRate(rate) {
  editingRate.value = rate;
  form.value = { buy_rate: rate.buy_rate, sell_rate: rate.sell_rate, date: rate.date };
  showCreateForm.value = true;
}

function cancelForm() {
  showCreateForm.value = false;
  editingRate.value = null;
  form.value = { buy_rate: 0, sell_rate: 0, date: new Date().toISOString().split('T')[0] };
  submitError.value = null;
}

async function handleSubmit() {
  submitError.value = null;
  loading.value = true;
  try {
    if (editingRate.value) {
      await api.put(`/rates/${editingRate.value.id}`, form.value);
    } else {
      await api.post('/rates', form.value);
    }
    await loadRates();
    cancelForm();
  } catch (err) {
    submitError.value = err.response?.data?.message || 'Ошибка сохранения';
  } finally {
    loading.value = false;
  }
}

async function deleteRate(id) {
  if (!confirm('Удалить курс?')) return;
  try {
    await api.delete(`/rates/${id}`);
    await loadRates();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка');
  }
}

function formatNumber(value) {
  return new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
}
</script>

<style scoped>
.rates-page { max-width: 1600px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 32px; font-weight: 600; color: #2c3e50; margin: 0; }
.form-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 600px; width: 90%; }
.form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.form-header h2 { margin: 0; font-size: 24px; font-weight: 600; color: #2c3e50; }
.btn-close { background: none; border: none; font-size: 32px; cursor: pointer; color: #666; padding: 0; width: 32px; height: 32px; line-height: 1; }
.btn-close:hover { color: #333; }
.form-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px; }
.form-input { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 15px; transition: all 0.2s; outline: none; }
.form-input:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
.alert { padding: 16px; border-radius: 8px; margin-bottom: 20px; }
.alert-error { background: #fee; border: 1px solid #fcc; color: #c33; }
.form-actions { display: flex; gap: 12px; margin-top: 30px; }
.loading, .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.table-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #2c3e50; border-bottom: 2px solid #e0e0e0; }
.data-table td { padding: 14px 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
.data-table tr:hover { background: #f8f9fa; }
.text-small { font-size: 12px; color: #666; }
.font-weight-bold { font-weight: 600; }
.action-buttons { display: flex; gap: 6px; }
.btn-action { width: 32px; height: 32px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: all 0.2s; color: white; }
.btn-info { background: #17a2b8; }
.btn-info:hover { background: #138496; }
.btn-danger { background: #dc3545; }
.btn-danger:hover { background: #c82333; }
.btn { padding: 12px 24px; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102,126,234,0.4); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-secondary { background: #6c757d; color: white; }
.btn-secondary:hover { background: #5a6268; }
</style>
