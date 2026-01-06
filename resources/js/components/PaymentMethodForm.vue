<template>
  <div class="payment-method-form">
    <div class="form-header">
      <label class="section-label">Методы оплаты *</label>
      <button type="button" @click="addMethod" class="btn-add-method" :disabled="!canAddMore">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Добавить метод
      </button>
    </div>

    <div class="methods-list">
      <div v-for="(method, index) in methods" :key="method.id" class="method-item">
        <div class="method-header">
          <span class="method-number">Метод #{{ index + 1 }}</span>
          <button
            v-if="methods.length > 1"
            type="button"
            @click="removeMethod(index)"
            class="btn-remove"
            title="Удалить метод"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <div class="method-fields">
          <div class="form-group">
            <label :for="`method-type-${method.id}`">Способ оплаты *</label>
            <select
              :id="`method-type-${method.id}`"
              v-model="method.method"
              class="form-select"
              required
              @change="onMethodTypeChange(index)"
            >
              <option value="">Выберите способ</option>
              <option value="cash">Наличные</option>
              <option value="card">Карта</option>
              <option value="transfer">Безналичный перевод</option>
              <option value="p2p">P2P перевод</option>
            </select>
          </div>

          <div class="form-group">
            <label :for="`method-amount-${method.id}`">Сумма *</label>
            <input
              :id="`method-amount-${method.id}`"
              v-model.number="method.amount"
              type="number"
              step="0.01"
              min="0"
              class="form-input"
              :class="{ 'error': method.amountError }"
              placeholder="0.00"
              required
              @input="validateAmounts"
            />
            <span v-if="method.amountError" class="error-message">
              {{ method.amountError }}
            </span>
          </div>

          <!-- Payment System for Card -->
          <div v-if="method.method === 'card'" class="form-group">
            <label :for="`method-system-${method.id}`">Платежная система</label>
            <select
              :id="`method-system-${method.id}`"
              v-model="method.payment_system"
              class="form-select"
            >
              <option value="">Не указано</option>
              <option value="uzcard">UzCard</option>
              <option value="humo">Humo</option>
              <option value="visa">Visa</option>
              <option value="mastercard">MasterCard</option>
              <option value="unionpay">UnionPay</option>
            </select>
          </div>

          <!-- Reference for Transfer/P2P -->
          <div v-if="method.method === 'transfer' || method.method === 'p2p' || method.method === 'card'" class="form-group">
            <label :for="`method-reference-${method.id}`">
              {{
                method.method === 'card' ? 'Последние 4 цифры карты' :
                method.method === 'transfer' ? 'Номер транзакции' :
                'Номер P2P операции'
              }}
            </label>
            <input
              :id="`method-reference-${method.id}`"
              v-model="method.reference"
              type="text"
              class="form-input"
              :placeholder="method.method === 'card' ? '1234' : 'Необязательно'"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Total Summary -->
    <div class="total-summary" :class="{ 'error': totalError }">
      <div class="summary-row">
        <span class="summary-label">Всего по методам:</span>
        <span class="summary-value">{{ formatMoney(totalMethodsAmount) }}</span>
      </div>
      <div class="summary-row">
        <span class="summary-label">Требуемая сумма:</span>
        <span class="summary-value">{{ formatMoney(requiredTotal) }}</span>
      </div>
      <div v-if="totalError" class="summary-row error-row">
        <span class="summary-label">Разница:</span>
        <span class="summary-value">{{ formatMoney(Math.abs(totalMethodsAmount - requiredTotal)) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  requiredTotal: {
    type: Number,
    required: true,
  },
  initialMethods: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['update:methods', 'validation-change']);

let methodIdCounter = 0;

const methods = ref([]);

// Initialize with one method or provided methods
if (props.initialMethods.length > 0) {
  methods.value = props.initialMethods.map(m => ({
    id: methodIdCounter++,
    method: m.method || '',
    amount: m.amount || 0,
    payment_system: m.payment_system || '',
    reference: m.reference || '',
    amountError: '',
  }));
} else {
  methods.value = [{
    id: methodIdCounter++,
    method: 'cash',
    amount: props.requiredTotal,
    payment_system: '',
    reference: '',
    amountError: '',
  }];
}

const totalMethodsAmount = computed(() => {
  return methods.value.reduce((sum, method) => sum + (parseFloat(method.amount) || 0), 0);
});

const totalError = computed(() => {
  const diff = Math.abs(totalMethodsAmount.value - props.requiredTotal);
  return diff > 0.01; // Allow 1 kopek tolerance for rounding
});

const canAddMore = computed(() => {
  return methods.value.length < 5; // Maximum 5 payment methods
});

const isValid = computed(() => {
  // Check if all methods have type and amount
  const allMethodsValid = methods.value.every(m =>
    m.method && m.amount > 0
  );

  // Check if total matches required
  return allMethodsValid && !totalError.value;
});

function addMethod() {
  if (!canAddMore.value) return;

  const remainingAmount = props.requiredTotal - totalMethodsAmount.value;

  methods.value.push({
    id: methodIdCounter++,
    method: '',
    amount: remainingAmount > 0 ? remainingAmount : 0,
    payment_system: '',
    reference: '',
    amountError: '',
  });
}

function removeMethod(index) {
  if (methods.value.length <= 1) return;
  methods.value.splice(index, 1);
  validateAmounts();
}

function onMethodTypeChange(index) {
  // Clear payment_system and reference when method type changes
  const method = methods.value[index];
  if (method.method !== 'card') {
    method.payment_system = '';
  }
  if (!['card', 'transfer', 'p2p'].includes(method.method)) {
    method.reference = '';
  }
}

function validateAmounts() {
  methods.value.forEach(method => {
    method.amountError = '';
  });

  if (totalError.value) {
    const diff = totalMethodsAmount.value - props.requiredTotal;
    if (diff > 0) {
      methods.value[methods.value.length - 1].amountError = 'Общая сумма превышает требуемую';
    } else {
      methods.value[methods.value.length - 1].amountError = 'Общая сумма меньше требуемой';
    }
  }
}

function formatMoney(value) {
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value || 0);
}

// Emit methods on change
watch(methods, () => {
  const cleanMethods = methods.value.map(m => ({
    method: m.method,
    amount: parseFloat(m.amount) || 0,
    payment_system: m.payment_system || null,
    reference: m.reference || null,
  }));

  emit('update:methods', cleanMethods);
}, { deep: true });

// Emit validation status on change
watch(isValid, (newVal) => {
  emit('validation-change', newVal);
});

// Watch required total changes
watch(() => props.requiredTotal, (newTotal) => {
  // If only one method, update its amount
  if (methods.value.length === 1) {
    methods.value[0].amount = newTotal;
  }
  validateAmounts();
});

// Initial validation and emit
validateAmounts();

// Emit initial validation status
emit('validation-change', isValid.value);
</script>

<style scoped>
.payment-method-form {
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  padding: 20px;
  background: #fafafa;
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-label {
  font-size: 16px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
}

.btn-add-method {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-add-method:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-add-method:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.methods-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 20px;
}

.method-item {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 16px;
  transition: all 0.2s;
}

.method-item:hover {
  border-color: #667eea;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
}

.method-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid #e0e0e0;
}

.method-number {
  font-size: 14px;
  font-weight: 600;
  color: #667eea;
}

.btn-remove {
  padding: 4px;
  background: transparent;
  border: none;
  color: #e74c3c;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-remove:hover {
  background: #fee;
}

.method-fields {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
  font-size: 13px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d0d0d0;
  border-radius: 6px;
  font-size: 14px;
  transition: all 0.2s;
  outline: none;
  background: white;
}

.form-input:focus,
.form-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error {
  border-color: #e74c3c;
}

.error-message {
  display: block;
  margin-top: 4px;
  color: #e74c3c;
  font-size: 12px;
}

.total-summary {
  background: white;
  border: 2px solid #10b981;
  border-radius: 8px;
  padding: 16px;
  margin-top: 20px;
}

.total-summary.error {
  border-color: #e74c3c;
  background: #fff5f5;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 15px;
}

.summary-row:last-child {
  margin-bottom: 0;
}

.summary-row.error-row {
  color: #e74c3c;
  font-weight: 600;
  padding-top: 8px;
  border-top: 1px solid #e74c3c;
  margin-top: 8px;
}

.summary-label {
  font-weight: 500;
  color: #555;
}

.summary-value {
  font-weight: 600;
  font-size: 16px;
  color: #2c3e50;
}

.total-summary.error .summary-value {
  color: #e74c3c;
}

@media (max-width: 768px) {
  .method-fields {
    grid-template-columns: 1fr;
  }

  .form-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .btn-add-method {
    width: 100%;
    justify-content: center;
  }
}
</style>
