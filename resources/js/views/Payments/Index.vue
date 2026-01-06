<template>
  <div class="payments-page">
    <div class="page-header">
      <h1 class="page-title">Платежи</h1>
      <router-link to="/payments/create" class="btn btn-primary">
        + Новый платеж
      </router-link>
    </div>

    <div class="filters-card">
      <div class="filters">
        <div class="filter-group">
          <label>Статус</label>
          <select v-model="filters.status" @change="loadPayments" class="form-select">
            <option value="">Все</option>
            <option value="pending">Ожидание</option>
            <option value="confirmed">Подтвержден</option>
            <option value="deleted">Удален</option>
            <option value="processed">Обработан</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Дата</label>
          <input
            type="date"
            v-model="filters.date"
            @change="loadPayments"
            class="form-input"
          />
        </div>

        <button @click="clearFilters" class="btn btn-secondary">Очистить</button>
      </div>
    </div>

    <div v-if="loading" class="loading">Загрузка...</div>

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else-if="payments.length === 0" class="empty-state">
      <p>Платежи не найдены</p>
    </div>

    <div v-else class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>№</th>
            <th>Дата/Время</th>
            <th>Плательщик</th>
            <th>Тип платежа</th>
            <th>Сумма</th>
            <th>Комиссия</th>
            <th>Итого</th>
            <th>Статус</th>
            <th>Кассир</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in payments" :key="payment.id">
            <td>{{ payment.list_number }}</td>
            <td>
              <div>{{ payment.date }}</div>
              <div class="text-small">{{ payment.time }}</div>
            </td>
            <td>{{ payment.payer_name }}</td>
            <td>{{ payment.payment_type?.name }}</td>
            <td>{{ formatNumber(payment.amount) }} {{ payment.currency }}</td>
            <td>{{ formatNumber(payment.commission) }} {{ payment.currency }}</td>
            <td class="font-weight-bold">{{ formatNumber(payment.total) }} {{ payment.currency }}</td>
            <td>
              <span class="badge" :class="`badge-${getStatusClass(payment.status)}`">
                {{ getStatusText(payment.status) }}
              </span>
            </td>
            <td>{{ payment.cashier?.full_name }}</td>
            <td>
              <div class="action-buttons">
                <button
                  @click="printReceipt(payment.id)"
                  class="btn-action btn-print"
                  title="Печать квитанции"
                >
                  🖨️
                </button>
                <button
                  v-if="authStore.isAdmin && payment.status === 'pending'"
                  @click="confirmPayment(payment.id)"
                  class="btn-action btn-success"
                  title="Подтвердить"
                >
                  ✓
                </button>
                <button
                  v-if="authStore.isAdmin"
                  @click="duplicatePayment(payment.id)"
                  class="btn-action btn-info"
                  title="Дублировать"
                >
                  📋
                </button>
                <button
                  v-if="authStore.isAdmin && payment.status === 'pending'"
                  @click="deletePayment(payment.id)"
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

    <!-- Receipt Dialog -->
    <div v-if="showReceiptDialog" class="modal-overlay" @click="closeReceipt">
      <div class="modal-content receipt-modal" @click.stop>
        <div class="modal-header">
          <h2>Дубликат квитанции</h2>
        </div>
        <div class="modal-body">
          <PaymentReceipt
            v-if="selectedPayment"
            :payment="selectedPayment"
            company-name="Paynes Kassa"
            :show-q-r-code="false"
          />
        </div>
        <div class="modal-footer">
          <button @click="printDuplicate" class="btn btn-primary">
            Печать дубликата
          </button>
          <button @click="closeReceipt" class="btn btn-secondary">
            Закрыть
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import PaymentReceipt from '@/components/PaymentReceipt.vue';
import { useAuthStore } from '@/stores/auth';

const payments = ref([]);
const meta = ref(null);
const loading = ref(false);
const error = ref(null);
const filters = ref({
  status: '',
  date: '',
  page: 1
});
const showReceiptDialog = ref(false);
const selectedPayment = ref(null);

// Auth store
const authStore = useAuthStore();

onMounted(() => {
  loadPayments();
});

async function loadPayments() {
  loading.value = true;
  error.value = null;

  try {
    const params = new URLSearchParams();
    if (filters.value.status) params.append('status', filters.value.status);
    if (filters.value.date) params.append('date', filters.value.date);
    params.append('page', filters.value.page);

    const response = await api.get(`/payments?${params}`);

    if (response.data.success) {
      payments.value = response.data.data.data;
      meta.value = response.data.data.meta;
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка загрузки платежей';
  } finally {
    loading.value = false;
  }
}

function clearFilters() {
  filters.value = { status: '', date: '', page: 1 };
  loadPayments();
}

function changePage(page) {
  filters.value.page = page;
  loadPayments();
}

async function confirmPayment(id) {
  if (!confirm('Подтвердить платеж?')) return;

  try {
    await api.post(`/payments/${id}/confirm`);
    await loadPayments();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка подтверждения платежа');
  }
}

async function duplicatePayment(id) {
  if (!confirm('Создать дубликат платежа?')) return;

  try {
    await api.post(`/payments/${id}/duplicate`);
    await loadPayments();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка создания дубликата');
  }
}

async function deletePayment(id) {
  if (!confirm('Удалить платеж?')) return;

  try {
    await api.delete(`/payments/${id}`);
    await loadPayments();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка удаления платежа');
  }
}

function formatNumber(value) {
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
}

function getStatusText(status) {
  const statuses = {
    pending: 'Ожидание',
    confirmed: 'Подтвержден',
    deleted: 'Удален',
    processed: 'Обработан'
  };
  return statuses[status] || status;
}

function getStatusClass(status) {
  const classes = {
    pending: 'warning',
    confirmed: 'success',
    deleted: 'danger',
    processed: 'info'
  };
  return classes[status] || 'default';
}

async function printReceipt(paymentId) {
  try {
    // Загружаем полные данные платежа
    const response = await api.get(`/payments/${paymentId}`);

    if (response.data.success) {
      selectedPayment.value = response.data.data;
      showReceiptDialog.value = true;
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка загрузки данных платежа');
  }
}

function closeReceipt() {
  showReceiptDialog.value = false;
  selectedPayment.value = null;
}

async function printDuplicate() {
  if (!selectedPayment.value) return;

  // Generate QR code first
  let qrCodeDataUrl = '';
  if (selectedPayment.value?.share_url) {
    try {
      const QRCode = (await import('qrcode')).default;
      qrCodeDataUrl = await QRCode.toDataURL(selectedPayment.value.share_url, {
        width: 60,
        margin: 1,
        color: {
          dark: '#000000',
          light: '#FFFFFF'
        }
      });
    } catch (error) {
      console.error('QR generation failed:', error);
    }
  }

  const printWindow = window.open('', '_blank');

  const receiptHTML = `
    <html>
      <head>
        <title>Дубликат квитанции - ${selectedPayment.value?.payment_number}</title>
        <style>
          @page {
            size: A4;
            margin: 0;
          }

          * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
          }

          html,
          body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
          }

          body {
            font-family: 'Verdana', Arial, sans-serif;
            padding: 5mm;
          }

          .receipt-wrapper {
            width: 190mm;
            margin: 0 auto;
            padding: 0;
          }

          .receipt-container {
            width: 190mm;
            height: 75mm;
            border: 1px solid #000;
            display: flex;
            background: white;
            font-family: 'Verdana', Arial, sans-serif;
            position: relative;
            overflow: hidden;
          }

          .duplicate-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 32pt;
            font-weight: bold;
            color: rgba(255, 0, 0, 0.15);
            z-index: 10;
            pointer-events: none;
          }

          .receipt-left {
            width: 50mm;
            border-right: 1px solid #000;
            padding: 2mm;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 1mm;
          }

          .receipt-label {
            font-size: 8pt;
            font-weight: bold;
            margin: 0;
            padding: 0.5mm 0;
            line-height: 1.2;
          }

          .qr-code {
            width: 16mm;
            height: 16mm;
            margin: 1mm auto;
            display: flex;
            align-items: center;
            justify-content: center;
          }

          .qr-code img {
            width: 16mm;
            height: 16mm;
            display: block;
          }

          .signature-field {
            margin-top: auto;
            padding-top: 1mm;
            text-align: left;
          }

          .signature-label {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 0.5mm;
          }

          .signature-line {
            border-top: 1px solid #000;
            width: 100%;
            margin-top: 2mm;
          }

          .receipt-right {
            flex: 1;
            display: flex;
            flex-direction: column;
          }

          .receipt-header {
            text-align: center;
            border-bottom: 1px solid #000;
            font-size: 8pt;
            padding: 1mm;
            font-weight: bold;
          }

          .receipt-details {
            padding: 2mm;
          }

          .detail-line {
            display: flex;
            font-size: 8pt;
            margin-bottom: 0.5mm;
            align-items: baseline;
          }

          .detail-label {
            font-weight: bold;
            min-width: 25mm;
            margin-right: 2mm;
          }

          .detail-value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding-bottom: 0.5mm;
          }

          .payer-name {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin: 2mm;
          }

          .purpose-section {
            text-align: center;
            margin: 0 2mm;
            border-top: 1px solid #000;
            padding-top: 1mm;
          }

          .purpose-text {
            font-size: 9pt;
            margin: 1mm 0;
            font-weight: 600;
          }

          .purpose-label {
            font-size: 7pt;
            color: #666;
            border-top: 1px solid #000;
            padding-top: 0.5mm;
          }

          .amounts-section {
            margin-top: auto;
            border-top: 1px solid #000;
          }

          .amount-row {
            display: flex;
            border-bottom: 1px solid #000;
          }

          .amount-label {
            font-size: 8pt;
            padding: 1.5mm 2mm;
            flex: 1;
          }

          .amount-value {
            font-size: 9pt;
            padding: 1.5mm 2mm;
            text-align: right;
            min-width: 60mm;
            white-space: nowrap;
            border-left: 1px solid #000;
          }

          .total-row {
            border-bottom: none;
          }

          .total-row .amount-label,
          .total-row .amount-value {
            font-weight: bold;
            font-size: 9pt;
          }

          @media print {
            * {
              -webkit-print-color-adjust: exact;
              print-color-adjust: exact;
            }

            body {
              margin: 0 !important;
              padding: 5mm !important;
            }

            .receipt-wrapper {
              width: 190mm !important;
              margin: 0 auto !important;
            }

            .receipt-container {
              width: 190mm !important;
              height: 75mm !important;
              page-break-inside: avoid;
              border: 1px solid #000 !important;
            }

            .receipt-left {
              width: 50mm !important;
              border-right: 1px solid #000 !important;
            }

            .amount-row {
              border-bottom: 1px solid #000 !important;
            }

            .amount-value {
              border-left: 1px solid #000 !important;
            }

            .detail-value {
              border-bottom: 1px solid #000 !important;
            }

            .duplicate-watermark {
              color: rgba(255, 0, 0, 0.15) !important;
            }
          }
        </style>
      </head>
      <body>
        <div class="receipt-wrapper">
          <div class="receipt-container">
            <div class="duplicate-watermark">ДУБЛИКАТ</div>
            <div class="receipt-left">
              <h4 class="receipt-label">Хабарнома</h4>
              <h4 class="receipt-label">Paynes Kassa</h4>
              <h4 class="receipt-label">${selectedPayment.value?.payment_method === 'cash' ? 'Наличный' : 'Безналичный'}</h4>
              <h4 class="receipt-label">${formatDate(selectedPayment.value?.created_at)}</h4>
              <h4 class="receipt-label">${formatTime(selectedPayment.value?.created_at)}</h4>
              ${qrCodeDataUrl ? `
                <div class="qr-code">
                  <img src="${qrCodeDataUrl}" alt="QR Code" />
                </div>
              ` : ''}
              <h4 class="receipt-label">№ ${selectedPayment.value?.payment_number || ''}</h4>
              <h4 class="receipt-label">${selectedPayment.value?.cashier?.full_name || ''}</h4>

              <div class="signature-field">
                <div class="signature-label">Подпись:</div>
                <div class="signature-line"></div>
              </div>
            </div>

            <div class="receipt-right">
              <h4 class="receipt-header">${selectedPayment.value?.payment_type?.name || ''}</h4>

              <div class="receipt-details">
                ${selectedPayment.value?.payment_type?.bank_name ? `
                  <div class="detail-line">
                    <span class="detail-label">Банк:</span>
                    <span class="detail-value">${selectedPayment.value.payment_type.bank_name}</span>
                  </div>
                ` : ''}
                ${selectedPayment.value?.payment_type?.account_number ? `
                  <div class="detail-line">
                    <span class="detail-label">Счет №:</span>
                    <span class="detail-value">${selectedPayment.value.payment_type.account_number}</span>
                  </div>
                ` : ''}
                ${selectedPayment.value?.payment_type?.mfo ? `
                  <div class="detail-line">
                    <span class="detail-label">МФО:</span>
                    <span class="detail-value">${selectedPayment.value.payment_type.mfo}</span>
                  </div>
                ` : ''}
                ${selectedPayment.value?.payment_type?.inn ? `
                  <div class="detail-line">
                    <span class="detail-label">ИНН:</span>
                    <span class="detail-value">${selectedPayment.value.payment_type.inn}</span>
                  </div>
                ` : ''}
                ${selectedPayment.value?.list_number ? `
                  <div class="detail-line">
                    <span class="detail-label">Лицевой счет:</span>
                    <span class="detail-value">${selectedPayment.value.list_number}</span>
                  </div>
                ` : ''}
              </div>

              <h4 class="payer-name">${selectedPayment.value?.payer_name || ''}</h4>

              ${selectedPayment.value?.purpose ? `
                <div class="purpose-section">
                  <p class="purpose-text">${selectedPayment.value.purpose}</p>
                  <p class="purpose-label">(Назначение платежа / Адрес)</p>
                </div>
              ` : ''}

              <div class="amounts-section">
                <div class="amount-row">
                  <span class="amount-label">Сумма:</span>
                  <span class="amount-value">${formatNumber(selectedPayment.value?.amount)} ${selectedPayment.value?.currency || ''}</span>
                </div>
                <div class="amount-row">
                  <span class="amount-label">Комиссия:</span>
                  <span class="amount-value">${formatNumber(selectedPayment.value?.commission_amount)} ${selectedPayment.value?.currency || ''}</span>
                </div>
                <div class="amount-row total-row">
                  <span class="amount-label">Итого:</span>
                  <span class="amount-value">${formatNumber(selectedPayment.value?.total_amount)} ${selectedPayment.value?.currency || ''}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </body>
    </html>
  `;

  printWindow.document.write(receiptHTML);
  printWindow.document.close();
  printWindow.focus();

  setTimeout(() => {
    printWindow.print();
    printWindow.close();
  }, 250);
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

function formatTime(dateString) {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('ru-RU', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
}
</script>

<style scoped>
.payments-page {
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

.badge-warning {
  background: #fff3cd;
  color: #856404;
}

.badge-danger {
  background: #f8d7da;
  color: #721c24;
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

.btn-success {
  background: #28a745;
  color: white;
}

.btn-success:hover {
  background: #218838;
}

.btn-info {
  background: #17a2b8;
  color: white;
}

.btn-info:hover {
  background: #138496;
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
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  display: inline-block;
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

.btn-print {
  background: #6c757d;
  color: white;
}

.btn-print:hover {
  background: #5a6268;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  animation: fadeIn 0.2s;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content {
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
  max-width: 850px;
  width: 95%;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s;
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

.modal-header {
  padding: 20px 24px;
  border-bottom: 2px solid #e0e0e0;
}

.modal-header h2 {
  margin: 0;
  font-size: 20px;
  color: #2c3e50;
  font-weight: 600;
}

.modal-body {
  padding: 10px;
  overflow-x: auto;
}

.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #e0e0e0;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

/* Receipt Modal Styles */
.receipt-modal {
  max-width: 850px;
  width: 95%;
}

.receipt-modal .modal-body {
  padding: 10px;
  overflow-x: auto;
}
</style>
