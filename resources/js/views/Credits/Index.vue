<template>
  <div class="credits-page">
    <div class="page-header">
      <h1 class="page-title">Кредиты</h1>
      <router-link to="/credits/create" class="btn btn-primary">
        + Новый кредит
      </router-link>
    </div>

    <div v-if="loading" class="loading">Загрузка...</div>
    <div v-else-if="credits.length === 0" class="empty-state">
      <p>Кредиты не найдены</p>
    </div>
    <div v-else class="table-card">
      <table class="data-table">
              <thead>
        <tr>
          <th>Дата/время</th>
          <th>Получатель</th>
          <th>Счет</th>
          <th>Филиал</th>
          <th>Сумма</th>
          <th>Статус</th>
          <th>Действия</th>
        </tr>
      </thead>
        <tbody>
          <tr v-for="credit in credits" :key="credit.id">
            <td>
              <div>{{ credit.date }}</div>
              <div class="text-small">{{ credit.time }}</div>
            </td>
            <td>{{ credit.recipient }}</td>
            <td class="font-mono">{{ credit.account_number }}</td>
            <td>{{ credit.branch }}</td>
            <td>{{ formatNumber(credit.amount ?? credit.debit) }} UZS</td>
            <td>
              <span class="badge" :class="`badge-${getStatusClass(credit.status)}`">
                {{ getStatusText(credit.status) }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button
                  v-if="authStore.isAdmin && credit.status === 'pending'"
                  @click="confirmCredit(credit.id)"
                  class="btn-action btn-success"
                  title="Подтвердить"
                >
                  ✓
                </button>
                <button
                  v-if="credit.status === 'pending'"
                  @click="deleteCredit(credit.id)"
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
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/services/api';

const authStore = useAuthStore();
const credits = ref([]);
const loading = ref(false);

onMounted(() => loadCredits());

async function loadCredits() {
  loading.value = true;
  try {
    const response = await api.get('/credits');
    if (response.data.success) {
      credits.value = response.data.data.data || response.data.data;
    }
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
}

async function confirmCredit(id) {
  if (!confirm('Подтвердить кредит?')) return;
  try {
    await api.post(`/credits/${id}/confirm`);
    await loadCredits();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка');
  }
}

async function deleteCredit(id) {
  if (!confirm('Удалить кредит?')) return;
  try {
    await api.delete(`/credits/${id}`);
    await loadCredits();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка');
  }
}

function formatNumber(value) {
  return new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
}

function getStatusText(status) {
  const map = { pending: 'Ожидание', confirmed: 'Подтвержден', deleted: 'Удален' };
  return map[status] || status;
}

function getStatusClass(status) {
  const map = { pending: 'warning', confirmed: 'success', deleted: 'danger' };
  return map[status] || 'default';
}
</script>

<style scoped>
.credits-page { max-width: 1600px; margin: 0 auto; }
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
.badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; }
.badge-success { background: #d4edda; color: #155724; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-danger { background: #f8d7da; color: #721c24; }
.action-buttons { display: flex; gap: 6px; }
.btn-action { width: 32px; height: 32px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: all 0.2s; color: white; }
.btn-success { background: #28a745; }
.btn-success:hover { background: #218838; }
.btn-danger { background: #dc3545; }
.btn-danger:hover { background: #c82333; }
.btn { padding: 10px 20px; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102,126,234,0.4); }
</style>



