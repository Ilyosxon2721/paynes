<template>
  <div class="receipt-wrapper">
    <div class="receipt-container" id="receipt-content">
      <div class="receipt-left">
        <h4 class="receipt-label">Хабарнома</h4>
        <h4 class="receipt-label">{{ companyName }}</h4>
        <h4 class="receipt-label">{{ payment?.payment_method === 'cash' ? 'Наличный' : 'Безналичный' }}</h4>
        <h4 class="receipt-label">{{ formatDate(payment?.created_at) }}</h4>
        <h4 class="receipt-label">{{ formatTime(payment?.created_at) }}</h4>
        <div class="qr-code" v-if="showQRCode && qrCodeDataUrl">
          <img :src="qrCodeDataUrl" alt="QR Code" class="qr-image" />
        </div>
        <h4 class="receipt-label">№ {{ payment?.payment_number }}</h4>
        <h4 class="receipt-label">{{ payment?.cashier?.full_name }}</h4>

        <div class="signature-field">
          <div class="signature-label">Подпись:</div>
          <div class="signature-line"></div>
        </div>
      </div>

      <div class="receipt-right">
        <h4 class="receipt-header">{{ payment?.payment_type?.name }}</h4>

        <div class="receipt-details">
          <div class="detail-line" v-if="payment?.payment_type?.bank_name">
            <span class="detail-label">Банк:</span>
            <span class="detail-value">{{ payment?.payment_type?.bank_name }}</span>
          </div>

          <div class="detail-line" v-if="payment?.payment_type?.account_number">
            <span class="detail-label">Счет №:</span>
            <span class="detail-value">{{ payment?.payment_type?.account_number }}</span>
          </div>

          <div class="detail-line" v-if="payment?.payment_type?.mfo">
            <span class="detail-label">МФО:</span>
            <span class="detail-value">{{ payment?.payment_type?.mfo }}</span>
          </div>

          <div class="detail-line" v-if="payment?.payment_type?.inn">
            <span class="detail-label">ИНН:</span>
            <span class="detail-value">{{ payment?.payment_type?.inn }}</span>
          </div>

          <div class="detail-line" v-if="payment?.list_number">
            <span class="detail-label">Лицевой счет:</span>
            <span class="detail-value">{{ payment?.list_number }}</span>
          </div>
        </div>

        <h4 class="payer-name">{{ payment?.payer_name }}</h4>

        <div class="purpose-section" v-if="payment?.purpose">
          <p class="purpose-text">{{ payment?.purpose }}</p>
          <p class="purpose-label">(Назначение платежа / Адрес)</p>
        </div>

        <div class="amounts-section">
          <div class="amount-row">
            <span class="amount-label">Сумма:</span>
            <span class="amount-value">{{ formatNumber(payment?.amount) }} {{ payment?.currency }}</span>
          </div>
          <div class="amount-row">
            <span class="amount-label">Комиссия:</span>
            <span class="amount-value">{{ formatNumber(payment?.commission_amount) }} {{ payment?.currency }}</span>
          </div>
          <div class="amount-row total-row">
            <span class="amount-label">Итого:</span>
            <span class="amount-value">{{ formatNumber(payment?.total_amount) }} {{ payment?.currency }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import QRCode from 'qrcode';

const props = defineProps({
  payment: {
    type: Object,
    required: true
  },
  companyName: {
    type: String,
    default: 'Paynes Kassa'
  },
  showQRCode: {
    type: Boolean,
    default: true
  }
});

// QR Code generation
const qrCodeDataUrl = ref('');

const generateQRCode = async () => {
  if (!props.payment?.share_url) return;

  try {
    qrCodeDataUrl.value = await QRCode.toDataURL(props.payment.share_url, {
      width: 60,
      margin: 1,
      color: {
        dark: '#000000',
        light: '#FFFFFF'
      }
    });
  } catch (error) {
    console.error('QR Code generation failed:', error);
  }
};

onMounted(() => {
  if (props.showQRCode) {
    generateQRCode();
  }
});

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
.receipt-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
  padding: 10px;
  box-sizing: border-box;
}

.receipt-container {
  width: 100%;
  max-width: 840px; /* Slightly wider than A4 to match print scale */
  height: 312px; /* Slightly taller to fit totals */
  border: 1px solid #000;
  display: flex;
  background: white;
  box-sizing: border-box;
  font-family: 'Verdana', Arial, sans-serif;
}

.receipt-left {
  width: 200px;
  border-right: 1px solid #000;
  padding: 8px;
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.receipt-label {
  font-family: 'Verdana', Arial, sans-serif;
  font-size: 10px;
  font-weight: bold;
  margin: 0;
  padding: 1px 0;
  line-height: 1.2;
}

.qr-code {
  width: 60px;
  height: 60px;
  margin: 5px auto;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
}

.qr-image {
  width: 60px;
  height: 60px;
  display: block;
}

.signature-field {
  margin-top: auto;
  padding-top: 5px;
  text-align: left;
}

.signature-label {
  font-size: 9px;
  font-weight: bold;
  margin-bottom: 2px;
}

.signature-line {
  border-top: 1px solid #000;
  width: 100%;
  margin-top: 10px;
}

.receipt-right {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 0;
}

.receipt-header {
  text-align: center;
  border-bottom: 1px solid #000;
  font-size: 9px;
  margin: 0;
  padding: 2px;
  font-weight: bold;
}

.receipt-details {
  padding: 3px 8px;
}

.detail-line {
  display: flex;
  font-size: 9px;
  margin-bottom: 1px;
  align-items: baseline;
}

.detail-label {
  font-weight: bold;
  min-width: 100px;
  margin-right: 5px;
}

.detail-value {
  flex: 1;
  border-bottom: 1px solid #000;
  padding-bottom: 2px;
  font-weight: normal;
}

.payer-name {
  text-align: center;
  font-size: 12px;
  font-weight: bold;
  margin: 4px 8px 2px 8px;
  font-family: 'Verdana', Arial, sans-serif;
}

.purpose-section {
  text-align: center;
  margin: 0 8px;
  border-top: 1px solid #000;
  padding-top: 2px;
}

.purpose-text {
  font-size: 10px;
  margin: 2px 0;
  font-weight: 600;
  min-height: 12px;
}

.purpose-label {
  font-size: 8px;
  color: #666;
  margin: 0;
  border-top: 1px solid #000;
  padding-top: 1px;
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
  font-size: 9px;
  padding: 2px 6px;
  flex: 1;
  font-weight: normal;
}

.amount-value {
  font-size: 10px;
  padding: 2px 6px;
  text-align: right;
  min-width: 150px;
  border-left: 1px solid #000;
  font-weight: normal;
}

.total-row {
  border-bottom: none;
}

.total-row .amount-label,
.total-row .amount-value {
  font-weight: bold;
  font-size: 10px;
}

/* Print styles */
@media print {
  * {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .receipt-wrapper {
    padding: 0;
    margin: 0;
  }

  .receipt-container {
    width: 190mm !important;
    max-width: 190mm !important;
    height: 75mm !important;
    page-break-inside: avoid;
    border: 1px solid #000 !important;
  }

  .receipt-left {
    width: 50mm !important;
    border-right: 1px solid #000 !important;
  }

  .receipt-right {
    flex: 1;
  }

  .amount-label {
    width: auto !important;
    flex: 1;
  }

  .amount-value {
    width: auto !important;
    min-width: 60mm;
  }

  .qr-code {
    border: none;
    background: white;
  }

  .signature-field {
    page-break-inside: avoid;
  }

  .detail-line {
    page-break-inside: avoid;
  }
}
</style>
