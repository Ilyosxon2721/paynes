<template>
  <div class="incashes-page">
    <div class="page-header">
      <h1 class="page-title">Инкассация</h1>
      <router-link to="/incashes/create" class="btn btn-primary">+ Новая инкассация</router-link>
    </div>

    <div v-if="loading" class="loading">Загрузка...</div>
    <div v-else-if="incashes.length === 0" class="empty-state"><p>Инкассации не найдены</p></div>
    <div v-else class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Дата/Время</th>
            <th>Счет</th>
            <th>Сумма</th>
            <th>Тип</th>
            <th>Статус</th>
            <th>Кассир</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="incash in incashes" :key="incash.id">
            <td>
              <div>{{ incash.date }}</div>
              <div class="text-small">{{ incash.time }}</div>
            </td>
            <td class="font-mono">{{ incash.account_number }}</td>
            <td class="font-weight-bold">{{ formatNumber(incash.amount) }} UZS</td>
            <td>{{ incash.type }}</td>
            <td>
              <span class="badge badge-info">{{ incash.status }}</span>
            </td>
            <td>{{ incash.cashier?.full_name }}</td>
            <td>
              <button @click="deleteIncash(incash.id)" class="btn-action btn-danger" title="Удалить">×</button>
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

const incashes = ref([]);
const loading = ref(false);

onMounted(() => loadIncashes());

async function loadIncashes() {
  loading.value = true;
  try {
    const response = await api.get('/incashes');
    if (response.data.success) {
      incashes.value = response.data.data.data || response.data.data;
    }
  } finally {
    loading.value = false;
  }
}

async function deleteIncash(id) {
  if (!confirm('Удалить инкассацию?')) return;
  try {
    await api.delete(`/incashes/${id}`);
    await loadIncashes();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка');
  }
}

function formatNumber(value) {
  return new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
}
</script>

<style scoped>
.incashes-page { max-width: 1600px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 32px; font-weight: 600; color: #2c3e50; margin: 0; }
.loading, .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.table-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8f9fa; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; color: #2c3e50; border-bottom: 2px solid #e0e0e0; }
.data-table td { padding: 14px 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
.data-table tr:hover { background: #f8f9fa; }
.text-small { font-size: 12px; color: #666; margin-top: 2px; }
.font-mono { font-family: monospace; font-size: 13px; }
.font-weight-bold { font-weight: 600; }
.badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; }
.badge-info { background: #d1ecf1; color: #0c5460; }
.btn-action { width: 32px; height: 32px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: all 0.2s; color: white; }
.btn-danger { background: #dc3545; }
.btn-danger:hover { background: #c82333; }
.btn { padding: 10px 20px; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102,126,234,0.4); }
</style>
