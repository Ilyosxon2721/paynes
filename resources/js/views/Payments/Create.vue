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
            <span class="info-value">{{ formatNumber(totalWithCommission) }} {{ form.currency }}</span>
          </div>
        </div>

        <!-- Payment Method Details -->
        <PaymentMethodForm
          v-if="totalWithCommission > 0"
          :required-total="totalWithCommission"
          @update:methods="handleMethodsUpdate"
          @validation-change="handleMethodsValidation"
        />

        <div v-if="submitError" class="alert alert-error">
          {{ submitError }}
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading || !methodsValid">
            {{ loading ? 'Создание...' : 'Создать платеж' }}
          </button>
          <router-link to="/payments" class="btn btn-secondary">
            Отмена
          </router-link>
        </div>
      </form>
    </div>

    <!-- Confirmation Dialog -->
    <div v-if="showConfirmDialog" class="modal-overlay" @click="showConfirmDialog = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Подтверждение платежа</h2>
        </div>
        <div class="modal-body">
          <p class="confirm-question">Вы уверены, что хотите провести платеж?</p>
          <div class="confirm-details">
            <div class="detail-row">
              <span class="detail-label">Плательщик:</span>
              <span class="detail-value">{{ form.payer_name }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Тип платежа:</span>
              <span class="detail-value">{{ selectedPaymentType?.name }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Сумма:</span>
              <span class="detail-value">{{ formatNumber(form.amount) }} {{ form.currency }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Комиссия:</span>
              <span class="detail-value">{{ formatNumber(calculatedCommission) }} {{ form.currency }}</span>
            </div>
            <div class="detail-row total">
              <span class="detail-label">Итого:</span>
              <span class="detail-value">{{ formatNumber(totalWithCommission) }} {{ form.currency }}</span>
            </div>

            <!-- Payment Methods Breakdown -->
            <div v-if="form.method_details.length > 0" class="methods-breakdown">
              <div class="methods-header">Методы оплаты:</div>
              <div v-for="(method, index) in form.method_details" :key="index" class="method-row">
                <span class="method-label">{{ getMethodLabel(method.method) }}:</span>
                <span class="method-value">{{ formatNumber(method.amount) }} {{ form.currency }}</span>
                <span v-if="method.payment_system" class="method-extra">({{ method.payment_system.toUpperCase() }})</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="confirmPayment" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Обработка...' : 'Подтвердить' }}
          </button>
          <button @click="showConfirmDialog = false" class="btn btn-secondary" :disabled="loading">
            Отмена
          </button>
        </div>
      </div>
    </div>

    <!-- Receipt Dialog -->
    <div v-if="showReceiptDialog" class="modal-overlay" @click="closeReceipt">
      <div class="modal-content receipt-modal" @click.stop>
        <div class="modal-header">
          <h2>Квитанция об оплате</h2>
        </div>
        <div class="modal-body">
          <PaymentReceipt
            :payment="createdPayment"
            company-name="Paynes Kassa"
            :show-q-r-code="false"
          />
        </div>
        <div class="modal-footer">
          <button @click="printReceipt" class="btn btn-primary">
            Печать квитанции
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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import PaymentReceipt from '@/components/PaymentReceipt.vue';
import PaymentMethodForm from '@/components/PaymentMethodForm.vue';

const router = useRouter();

const form = ref({
  payment_type_id: '',
  payer_name: '',
  purpose: '',
  amount: 0,
  currency: 'UZS',
  list_number: '',
  method_details: []
});

const paymentTypes = ref([]);
const errors = ref({});
const submitError = ref(null);
const loading = ref(false);
const showConfirmDialog = ref(false);
const showReceiptDialog = ref(false);
const createdPayment = ref(null);
const methodsValid = ref(false);

const selectedPaymentType = computed(() => {
  return paymentTypes.value.find(t => t.id === form.value.payment_type_id);
});

const calculatedCommission = computed(() => {
  if (!selectedPaymentType.value || !form.value.amount) return 0;

  const percentCommission = form.value.amount * (selectedPaymentType.value.commission_percentage / 100);
  return Math.max(percentCommission, parseFloat(selectedPaymentType.value.commission_fixed || 0));
});

const totalWithCommission = computed(() => {
  return form.value.amount + calculatedCommission.value;
});

function handleMethodsUpdate(methods) {
  form.value.method_details = methods;
}

function handleMethodsValidation(isValid) {
  methodsValid.value = isValid;
}

function getMethodLabel(method) {
  const labels = {
    cash: 'Наличные',
    card: 'Карта',
    transfer: 'Безналичный перевод',
    p2p: 'P2P перевод'
  };
  return labels[method] || method;
}

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

  // Show confirmation dialog instead of submitting directly
  showConfirmDialog.value = true;
}

async function confirmPayment() {
  loading.value = true;
  errors.value = {};
  submitError.value = null;

  try {
    const response = await api.post('/payments', form.value);

    if (response.data.success) {
      createdPayment.value = response.data.data;
      showConfirmDialog.value = false;
      showReceiptDialog.value = true;
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
    }
    submitError.value = err.response?.data?.message || 'Ошибка при создании платежа';
    showConfirmDialog.value = false;
  } finally {
    loading.value = false;
  }
}

function closeReceipt() {
  showReceiptDialog.value = false;
  router.push({ name: 'Payments' });
}

async function printReceipt() {
  // Generate QR code first
  let qrCodeDataUrl = '';
  if (createdPayment.value?.share_url) {
    try {
      const QRCode = (await import('qrcode')).default;
      qrCodeDataUrl = await QRCode.toDataURL(createdPayment.value.share_url, {
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
        <title>Квитанция - ${createdPayment.value?.payment_number}</title>
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
            overflow: hidden;
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
          }
        </style>
      </head>
      <body>
        <div class="receipt-wrapper">
          <div class="receipt-container">
            <div class="receipt-left">
              <h4 class="receipt-label">Хабарнома</h4>
              <h4 class="receipt-label">Paynes Kassa</h4>
              <h4 class="receipt-label">${createdPayment.value?.payment_method === 'cash' ? 'Наличный' : 'Безналичный'}</h4>
              <h4 class="receipt-label">${formatDate(createdPayment.value?.created_at)}</h4>
              <h4 class="receipt-label">${formatTime(createdPayment.value?.created_at)}</h4>
              ${qrCodeDataUrl ? `
                <div class="qr-code">
                  <img src="${qrCodeDataUrl}" alt="QR Code" />
                </div>
              ` : ''}
              <h4 class="receipt-label">№ ${createdPayment.value?.payment_number || ''}</h4>
              <h4 class="receipt-label">${createdPayment.value?.cashier?.full_name || ''}</h4>

              <div class="signature-field">
                <div class="signature-label">Подпись:</div>
                <div class="signature-line"></div>
              </div>
            </div>

            <div class="receipt-right">
              <h4 class="receipt-header">${createdPayment.value?.payment_type?.name || ''}</h4>

              <div class="receipt-details">
                ${createdPayment.value?.payment_type?.bank_name ? `
                  <div class="detail-line">
                    <span class="detail-label">Банк:</span>
                    <span class="detail-value">${createdPayment.value.payment_type.bank_name}</span>
                  </div>
                ` : ''}
                ${createdPayment.value?.payment_type?.account_number ? `
                  <div class="detail-line">
                    <span class="detail-label">Счет №:</span>
                    <span class="detail-value">${createdPayment.value.payment_type.account_number}</span>
                  </div>
                ` : ''}
                ${createdPayment.value?.payment_type?.mfo ? `
                  <div class="detail-line">
                    <span class="detail-label">МФО:</span>
                    <span class="detail-value">${createdPayment.value.payment_type.mfo}</span>
                  </div>
                ` : ''}
                ${createdPayment.value?.payment_type?.inn ? `
                  <div class="detail-line">
                    <span class="detail-label">ИНН:</span>
                    <span class="detail-value">${createdPayment.value.payment_type.inn}</span>
                  </div>
                ` : ''}
                ${createdPayment.value?.list_number ? `
                  <div class="detail-line">
                    <span class="detail-label">Лицевой счет:</span>
                    <span class="detail-value">${createdPayment.value.list_number}</span>
                  </div>
                ` : ''}
              </div>

              <h4 class="payer-name">${createdPayment.value?.payer_name || ''}</h4>

              ${createdPayment.value?.purpose ? `
                <div class="purpose-section">
                  <p class="purpose-text">${createdPayment.value.purpose}</p>
                  <p class="purpose-label">(Назначение платежа / Адрес)</p>
                </div>
              ` : ''}

              <div class="amounts-section">
                <div class="amount-row">
                  <span class="amount-label">Сумма:</span>
                  <span class="amount-value">${formatNumber(createdPayment.value?.amount)} ${createdPayment.value?.currency || ''}</span>
                </div>
                <div class="amount-row">
                  <span class="amount-label">Комиссия:</span>
                  <span class="amount-value">${formatNumber(createdPayment.value?.commission_amount)} ${createdPayment.value?.currency || ''}</span>
                </div>
                <div class="amount-row total-row">
                  <span class="amount-label">Итого:</span>
                  <span class="amount-value">${formatNumber(createdPayment.value?.total_amount)} ${createdPayment.value?.currency || ''}</span>
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

function formatNumber(value) {
  if (!value) return '0.00';
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
  max-width: 500px;
  width: 90%;
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
  padding: 24px;
}

.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #e0e0e0;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

/* Confirmation Dialog Styles */
.confirm-question {
  font-size: 16px;
  color: #333;
  margin: 0 0 20px 0;
  font-weight: 500;
}

.confirm-details {
  background: #f8f9fa;
  padding: 16px;
  border-radius: 8px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  font-size: 14px;
}

.detail-row:last-child {
  margin-bottom: 0;
}

.detail-row.total {
  border-top: 2px solid #dee2e6;
  padding-top: 10px;
  margin-top: 10px;
  font-weight: 600;
  font-size: 16px;
  color: #1A77C9;
}

.detail-label {
  color: #666;
}

.detail-value {
  font-weight: 500;
  color: #333;
}

/* Payment Methods Breakdown */
.methods-breakdown {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #dee2e6;
}

.methods-header {
  font-size: 13px;
  font-weight: 600;
  color: #666;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.method-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
  font-size: 14px;
}

.method-row:last-child {
  margin-bottom: 0;
}

.method-label {
  color: #666;
  min-width: 140px;
}

.method-value {
  font-weight: 600;
  color: #2c3e50;
}

.method-extra {
  font-size: 12px;
  color: #999;
  font-weight: 500;
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
