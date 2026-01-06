<template>
  <div class="incashes-container">
    <div class="page-header">
      <h1>Инкассация</h1>
      <div class="header-actions">
        <button @click="printReport" class="btn btn-print" title="Печать отчета">
          🖨️ Печать отчета
        </button>
        <button @click="exportToExcel" class="btn btn-export" title="Экспорт в Excel">
          📊 Excel
        </button>
        <button @click="openCreateDialog('income')" class="btn btn-success">
          ↓ Приход
        </button>
        <button @click="openCreateDialog('expense')" class="btn btn-danger">
          ↑ Расход
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
      <div class="filters">
        <div class="filter-group">
          <label>Дата:</label>
          <input type="date" v-model="filters.date" @change="loadIncashes" />
        </div>
        <div class="filter-group">
          <label>Счет:</label>
          <select v-model="filters.account_number" @change="loadIncashes">
            <option value="">Все</option>
            <option value="001">001 - Наличные UZS</option>
            <option value="002">002 - Безналичные UZS</option>
            <option value="003">003 - Наличные RUB</option>
            <option value="840">840 - Наличные USD</option>
          </select>
        </div>
        <button @click="resetFilters" class="btn btn-secondary">Сбросить</button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="summary-card income">
        <div class="summary-icon">↓</div>
        <div class="summary-info">
          <div class="summary-label">Приход</div>
          <div class="summary-value">{{ formatNumber(summary.income) }} UZS</div>
          <div class="summary-count">{{ summary.income_count }} операций</div>
        </div>
      </div>
      <div class="summary-card expense">
        <div class="summary-icon">↑</div>
        <div class="summary-info">
          <div class="summary-label">Расход</div>
          <div class="summary-value">{{ formatNumber(summary.expense) }} UZS</div>
          <div class="summary-count">{{ summary.expense_count }} операций</div>
        </div>
      </div>
      <div class="summary-card balance">
        <div class="summary-icon">≈</div>
        <div class="summary-info">
          <div class="summary-label">Баланс</div>
          <div class="summary-value" :class="{ negative: summary.balance < 0 }">
            {{ formatNumber(summary.balance) }} UZS
          </div>
          <div class="summary-count">{{ summary.total_count }} операций</div>
        </div>
      </div>
    </div>

    <!-- Incashes Table -->
    <div class="table-card">
      <div v-if="loading" class="loading">Загрузка...</div>
      <div v-else-if="incashes.length === 0" class="empty-state">
        Нет инкассаций за выбранный период
      </div>
      <table v-else class="incashes-table">
        <thead>
          <tr>
            <th>Дата</th>
            <th>Время</th>
            <th>Тип</th>
            <th>Счет</th>
            <th>Сумма</th>
            <th>Кассир</th>
            <th>Статус</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="incash in incashes" :key="incash.id" :class="incash.type">
            <td>{{ formatDate(incash.date) }}</td>
            <td>{{ incash.time }}</td>
            <td>
              <span class="type-badge" :class="incash.type">
                {{ incash.type === 'income' ? '↓ Приход' : '↑ Расход' }}
              </span>
            </td>
            <td>{{ getAccountName(incash.account_number) }}</td>
            <td class="amount" :class="incash.type">
              {{ incash.type === 'income' ? '+' : '-' }}{{ formatNumber(incash.amount) }}
            </td>
            <td>{{ incash.cashier?.full_name }}</td>
            <td>
              <span class="status-badge" :class="incash.status">
                {{ getStatusText(incash.status) }}
              </span>
            </td>
            <td class="actions">
              <button
                v-if="authStore.isAdmin && incash.status === 'pending'"
                @click="confirmIncash(incash.id)"
                class="btn-icon btn-success"
                title="Подтвердить"
              >
                ✓
              </button>
              <button
                v-if="authStore.isAdmin"
                @click="deleteIncash(incash.id)"
                class="btn-icon btn-danger"
                title="Удалить"
              >
                ×
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination">
        <button
          @click="changePage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="btn btn-secondary"
        >
          ← Назад
        </button>
        <span class="page-info">Страница {{ currentPage }} из {{ totalPages }}</span>
        <button
          @click="changePage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="btn btn-secondary"
        >
          Вперед →
        </button>
      </div>
    </div>

    <!-- Create Dialog -->
    <div v-if="showCreateDialog" class="modal-overlay" @click.self="closeCreateDialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ dialogType === 'income' ? 'Приход (Инкассация)' : 'Расход (Выдача)' }}</h3>
          <button @click="closeCreateDialog" class="close-btn">×</button>
        </div>
        <form @submit.prevent="createIncash" class="modal-body">
          <div class="form-group">
            <label>Счет: *</label>
            <select v-model="form.account_number" required>
              <option value="">Выберите счет</option>
              <option value="001">001 - Наличные UZS</option>
              <option value="002">002 - Безналичные UZS</option>
              <option value="003">003 - Наличные RUB</option>
              <option value="840">840 - Наличные USD</option>
            </select>
          </div>
          <div class="form-group">
            <label>Сумма: *</label>
            <input
              type="number"
              v-model.number="form.amount"
              step="0.01"
              min="0"
              required
              placeholder="0.00"
            />
          </div>
        </form>
        <div class="modal-footer">
          <button @click="closeCreateDialog" class="btn btn-secondary">Отмена</button>
          <button
            @click="createIncash"
            :disabled="creating"
            class="btn"
            :class="dialogType === 'income' ? 'btn-success' : 'btn-danger'"
          >
            {{ creating ? 'Создание...' : 'Создать' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/services/api';

const authStore = useAuthStore();

const loading = ref(false);
const creating = ref(false);
const incashes = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const total = ref(0);

const showCreateDialog = ref(false);
const dialogType = ref('income');

const filters = reactive({
  date: '',
  account_number: ''
});

const form = reactive({
  account_number: '',
  amount: 0,
  type: 'income'
});

const summary = computed(() => {
  const result = {
    income: 0,
    expense: 0,
    income_count: 0,
    expense_count: 0,
    total_count: 0,
    balance: 0
  };

  incashes.value.forEach(incash => {
    if (incash.type === 'income') {
      result.income += parseFloat(incash.amount);
      result.income_count++;
    } else {
      result.expense += parseFloat(incash.amount);
      result.expense_count++;
    }
    result.total_count++;
  });

  result.balance = result.income - result.expense;
  return result;
});

async function loadIncashes() {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: 20,
      ...filters
    };

    const response = await api.get('/incashes', { params });

    if (response.data.success) {
      incashes.value = response.data.data;
      total.value = response.data.meta?.total || 0;
      totalPages.value = response.data.meta?.last_page || 1;
    }
  } catch (error) {
    console.error('Error loading incashes:', error);
    alert('Ошибка при загрузке инкассаций');
  } finally {
    loading.value = false;
  }
}

function openCreateDialog(type) {
  dialogType.value = type;
  form.type = type;
  form.account_number = '';
  form.amount = 0;
  showCreateDialog.value = true;
}

function closeCreateDialog() {
  showCreateDialog.value = false;
}

async function createIncash() {
  if (!form.account_number || form.amount <= 0) {
    showNotification('Заполните все обязательные поля', 'error');
    return;
  }

  // Валидация баланса при расходе
  if (form.type === 'expense') {
    const currentBalance = summary.value.balance;
    if (form.amount > currentBalance) {
      const proceed = confirm(
        `⚠️ Предупреждение!\n\n` +
        `Расход (${formatNumber(form.amount)} UZS) превышает текущий баланс (${formatNumber(currentBalance)} UZS).\n\n` +
        `Баланс станет отрицательным: ${formatNumber(currentBalance - form.amount)} UZS\n\n` +
        `Продолжить?`
      );
      if (!proceed) return;
    }
  }

  creating.value = true;
  try {
    const response = await api.post('/incashes', {
      account_number: form.account_number,
      amount: form.amount,
      type: form.type
    });

    if (response.data.success) {
      showNotification(
        `${form.type === 'income' ? '✅ Приход' : '⚠️ Расход'} успешно создан на сумму ${formatNumber(form.amount)} UZS`,
        'success'
      );
      closeCreateDialog();
      await loadIncashes();
    }
  } catch (error) {
    console.error('Error creating incash:', error);
    showNotification(error.response?.data?.message || 'Ошибка при создании инкассации', 'error');
  } finally {
    creating.value = false;
  }
}

async function confirmIncash(id) {
  if (!confirm('Подтвердить инкассацию?')) return;

  try {
    const response = await api.post(`/incashes/${id}/confirm`);
    if (response.data.success) {
      showNotification('✅ Инкассация успешно подтверждена', 'success');
      await loadIncashes();
    }
  } catch (error) {
    console.error('Error confirming incash:', error);
    showNotification(error.response?.data?.message || 'Ошибка при подтверждении', 'error');
  }
}

async function deleteIncash(id) {
  if (!confirm('Удалить инкассацию?')) return;

  try {
    const response = await api.delete(`/incashes/${id}`);
    if (response.data.success) {
      showNotification('🗑️ Инкассация удалена', 'info');
      await loadIncashes();
    }
  } catch (error) {
    console.error('Error deleting incash:', error);
    showNotification(error.response?.data?.message || 'Ошибка при удалении', 'error');
  }
}

function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.classList.add('show');
  }, 10);

  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}

function changePage(page) {
  if (page < 1 || page > totalPages.value) return;
  currentPage.value = page;
  loadIncashes();
}

function resetFilters() {
  filters.date = '';
  filters.account_number = '';
  currentPage.value = 1;
  loadIncashes();
}

function formatNumber(value) {
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value || 0);
}

function formatDate(dateString) {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('ru-RU', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  }).format(date);
}

function getAccountName(number) {
  const accounts = {
    '001': '001 - Наличные UZS',
    '002': '002 - Безналичные UZS',
    '003': '003 - Наличные RUB',
    '840': '840 - Наличные USD'
  };
  return accounts[number] || number;
}

function getStatusText(status) {
  const statuses = {
    pending: 'Ожидание',
    confirmed: 'Подтверждено',
    deleted: 'Удалено'
  };
  return statuses[status] || status;
}

function printReport() {
  const printWindow = window.open('', '_blank');
  const filterText = filters.date ? `за ${formatDate(filters.date)}` : 'за весь период';
  const accountText = filters.account_number ? ` по счету ${getAccountName(filters.account_number)}` : '';

  printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Отчет по инкассации</title>
      <meta charset="utf-8">
      <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
        h2 { text-align: center; font-size: 14px; margin-top: 0; color: #666; }
        .summary { display: flex; justify-content: space-around; margin: 20px 0; }
        .summary-item { text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; flex: 1; margin: 0 5px; }
        .summary-item.income { background: #f0fdf4; }
        .summary-item.expense { background: #fef2f2; }
        .summary-item.balance { background: #dbeafe; }
        .summary-label { font-size: 11px; color: #666; }
        .summary-value { font-size: 16px; font-weight: bold; margin: 5px 0; }
        .summary-count { font-size: 10px; color: #999; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f9fafb; font-weight: bold; }
        tr.income { background: #f0fdf4; }
        tr.expense { background: #fef2f2; }
        .amount.income { color: #10b981; }
        .amount.expense { color: #ef4444; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 11px; }
        @media print {
          button { display: none; }
        }
      </style>
    </head>
    <body>
      <h1>Отчет по инкассации</h1>
      <h2>${filterText}${accountText}</h2>

      <div class="summary">
        <div class="summary-item income">
          <div class="summary-label">Приход</div>
          <div class="summary-value">${formatNumber(summary.value.income)} UZS</div>
          <div class="summary-count">${summary.value.income_count} операций</div>
        </div>
        <div class="summary-item expense">
          <div class="summary-label">Расход</div>
          <div class="summary-value">${formatNumber(summary.value.expense)} UZS</div>
          <div class="summary-count">${summary.value.expense_count} операций</div>
        </div>
        <div class="summary-item balance">
          <div class="summary-label">Баланс</div>
          <div class="summary-value">${formatNumber(summary.value.balance)} UZS</div>
          <div class="summary-count">${summary.value.total_count} операций</div>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th>№</th>
            <th>Дата</th>
            <th>Время</th>
            <th>Тип</th>
            <th>Счет</th>
            <th>Сумма</th>
            <th>Кассир</th>
            <th>Статус</th>
          </tr>
        </thead>
        <tbody>
          ${incashes.value.map((incash, index) => `
            <tr class="${incash.type}">
              <td>${index + 1}</td>
              <td>${formatDate(incash.date)}</td>
              <td>${incash.time}</td>
              <td>${incash.type === 'income' ? '↓ Приход' : '↑ Расход'}</td>
              <td>${getAccountName(incash.account_number)}</td>
              <td class="amount ${incash.type}">${incash.type === 'income' ? '+' : '-'}${formatNumber(incash.amount)}</td>
              <td>${incash.cashier?.full_name || '-'}</td>
              <td>${getStatusText(incash.status)}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>

      <div class="footer">
        <div>Дата печати: ${new Date().toLocaleString('ru-RU')}</div>
        <div>Пользователь: ${authStore.user?.full_name || '-'}</div>
      </div>

      <script>
        window.onload = function() {
          window.print();
        };
      <\/script>
    </body>
    </html>
  `);
  printWindow.document.close();
}

function exportToExcel() {
  const filterText = filters.date ? `за ${formatDate(filters.date)}` : 'за весь период';
  const accountText = filters.account_number ? ` по счету ${getAccountName(filters.account_number)}` : '';

  let csv = '\uFEFF'; // BOM for UTF-8
  csv += `Отчет по инкассации ${filterText}${accountText}\n\n`;

  csv += 'Сводка\n';
  csv += `Приход,${formatNumber(summary.value.income)} UZS,${summary.value.income_count} операций\n`;
  csv += `Расход,${formatNumber(summary.value.expense)} UZS,${summary.value.expense_count} операций\n`;
  csv += `Баланс,${formatNumber(summary.value.balance)} UZS,${summary.value.total_count} операций\n\n`;

  csv += 'Детализация\n';
  csv += '№,Дата,Время,Тип,Счет,Сумма,Кассир,Статус\n';

  incashes.value.forEach((incash, index) => {
    csv += `${index + 1},`;
    csv += `${formatDate(incash.date)},`;
    csv += `${incash.time},`;
    csv += `${incash.type === 'income' ? 'Приход' : 'Расход'},`;
    csv += `${getAccountName(incash.account_number)},`;
    csv += `${incash.type === 'income' ? '+' : '-'}${formatNumber(incash.amount)},`;
    csv += `${incash.cashier?.full_name || '-'},`;
    csv += `${getStatusText(incash.status)}\n`;
  });

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);
  const fileName = `incash_report_${new Date().toISOString().slice(0, 10)}.csv`;

  link.setAttribute('href', url);
  link.setAttribute('download', fileName);
  link.style.visibility = 'hidden';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

onMounted(() => {
  loadIncashes();
});
</script>

<style scoped>
.incashes-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.page-header h1 {
  font-size: 32px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-success {
  background: #10b981;
  color: white;
}

.btn-success:hover {
  background: #059669;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-secondary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-print {
  background: #8b5cf6;
  color: white;
}

.btn-print:hover {
  background: #7c3aed;
}

.btn-export {
  background: #0891b2;
  color: white;
}

.btn-export:hover {
  background: #0e7490;
}

.filters-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 20px;
  align-items: flex-end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.filter-group label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.filter-group input,
.filter-group select {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  min-width: 200px;
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.summary-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  gap: 20px;
}

.summary-icon {
  font-size: 48px;
  width: 70px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.summary-card.income .summary-icon {
  background: #d1fae5;
  color: #10b981;
}

.summary-card.expense .summary-icon {
  background: #fee2e2;
  color: #ef4444;
}

.summary-card.balance .summary-icon {
  background: #dbeafe;
  color: #3b82f6;
}

.summary-info {
  flex: 1;
}

.summary-label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 8px;
}

.summary-value {
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.summary-value.negative {
  color: #ef4444;
}

.summary-count {
  font-size: 13px;
  color: #9ca3af;
}

.table-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.loading,
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #6b7280;
}

.incashes-table {
  width: 100%;
  border-collapse: collapse;
}

.incashes-table th {
  background: #f9fafb;
  padding: 12px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 2px solid #e5e7eb;
}

.incashes-table td {
  padding: 12px;
  border-bottom: 1px solid #f3f4f6;
}

.incashes-table tr.income {
  background: #f0fdf4;
}

.incashes-table tr.expense {
  background: #fef2f2;
}

.type-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
}

.type-badge.income {
  background: #d1fae5;
  color: #065f46;
}

.type-badge.expense {
  background: #fee2e2;
  color: #991b1b;
}

.amount {
  font-weight: 600;
  font-family: monospace;
}

.amount.income {
  color: #10b981;
}

.amount.expense {
  color: #ef4444;
}

.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.confirmed {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.deleted {
  background: #f3f4f6;
  color: #6b7280;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn-icon {
  width: 32px;
  height: 32px;
  padding: 0;
  border: none;
  border-radius: 6px;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon.btn-success {
  background: #10b981;
  color: white;
}

.btn-icon.btn-success:hover {
  background: #059669;
}

.btn-icon.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-icon.btn-danger:hover {
  background: #dc2626;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 24px;
}

.page-info {
  font-size: 14px;
  color: #6b7280;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: none;
  border: none;
  font-size: 28px;
  color: #9ca3af;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: #6b7280;
}

.modal-body {
  padding: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}
</style>

<style>
/* Global notification styles */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 16px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  z-index: 9999;
  opacity: 0;
  transform: translateX(400px);
  transition: all 0.3s ease-in-out;
  max-width: 400px;
}

.notification.show {
  opacity: 1;
  transform: translateX(0);
}

.notification-success {
  background: #10b981;
  color: white;
}

.notification-error {
  background: #ef4444;
  color: white;
}

.notification-info {
  background: #3b82f6;
  color: white;
}

.notification-warning {
  background: #f59e0b;
  color: white;
}
</style>
