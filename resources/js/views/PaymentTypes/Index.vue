<template>
  <div class="payment-types-page">
    <div class="page-header">
      <h1 class="page-title">Типы платежей</h1>
      <button @click="openCreateModal" class="btn btn-primary">
        + Добавить тип платежа
      </button>
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
            <th>Действия</th>
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
            <td>
              <div class="action-buttons">
                <button
                  @click="openEditModal(type)"
                  class="btn-action btn-edit"
                  title="Редактировать"
                >
                  ✏️
                </button>
                <button
                  @click="deletePaymentType(type.id)"
                  class="btn-action btn-delete"
                  title="Удалить"
                >
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>{{ isEditMode ? 'Редактировать тип платежа' : 'Добавить тип платежа' }}</h2>
        </div>
        <div class="modal-body">
          <form @submit.prevent="savePaymentType">
            <div class="form-group">
              <label for="name">Название *</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="form-input"
                :class="{ 'error': formErrors.name }"
                required
              />
              <span v-if="formErrors.name" class="error-message">{{ formErrors.name[0] }}</span>
            </div>

            <div class="form-group">
              <label for="organization">Организация</label>
              <input
                id="organization"
                v-model="form.organization"
                type="text"
                class="form-input"
              />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="bank">Банк</label>
                <input
                  id="bank"
                  v-model="form.bank"
                  type="text"
                  class="form-input"
                />
              </div>

              <div class="form-group">
                <label for="account_number">Номер счета</label>
                <input
                  id="account_number"
                  v-model="form.account_number"
                  type="text"
                  class="form-input"
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="mfo">МФО</label>
                <input
                  id="mfo"
                  v-model="form.mfo"
                  type="text"
                  class="form-input"
                />
              </div>

              <div class="form-group">
                <label for="inn">ИНН</label>
                <input
                  id="inn"
                  v-model="form.inn"
                  type="text"
                  class="form-input"
                />
              </div>
            </div>

            <div class="form-group">
              <label for="treasury_account">Казначейский счет</label>
              <input
                id="treasury_account"
                v-model="form.treasury_account"
                type="text"
                class="form-input"
              />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="commission_percentage">Комиссия процент (%)</label>
                <input
                  id="commission_percentage"
                  v-model.number="form.commission_percentage"
                  type="number"
                  step="0.01"
                  class="form-input"
                  placeholder="0.00"
                />
              </div>

              <div class="form-group">
                <label for="commission_fixed">Комиссия фиксированная (UZS)</label>
                <input
                  id="commission_fixed"
                  v-model.number="form.commission_fixed"
                  type="number"
                  step="0.01"
                  class="form-input"
                  placeholder="0.00"
                />
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Сохранение...' : 'Сохранить' }}
              </button>
              <button type="button" @click="closeModal" class="btn btn-secondary">
                Отмена
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const paymentTypes = ref([]);
const loading = ref(false);
const error = ref(null);
const showModal = ref(false);
const isEditMode = ref(false);
const editingId = ref(null);
const saving = ref(false);
const formErrors = ref({});

const defaultForm = {
  name: '',
  organization: '',
  bank: '',
  account_number: '',
  mfo: '',
  inn: '',
  treasury_account: '',
  commission_percentage: 0,
  commission_fixed: 0
};

const form = ref({ ...defaultForm });

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

function openCreateModal() {
  isEditMode.value = false;
  editingId.value = null;
  form.value = { ...defaultForm };
  formErrors.value = {};
  showModal.value = true;
}

function openEditModal(paymentType) {
  isEditMode.value = true;
  editingId.value = paymentType.id;
  form.value = {
    name: paymentType.name,
    organization: paymentType.organization || '',
    bank: paymentType.bank || '',
    account_number: paymentType.account_number || '',
    mfo: paymentType.mfo || '',
    inn: paymentType.inn || '',
    treasury_account: paymentType.treasury_account || '',
    commission_percentage: paymentType.commission_percentage || 0,
    commission_fixed: paymentType.commission_fixed || 0
  };
  formErrors.value = {};
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  form.value = { ...defaultForm };
  formErrors.value = {};
}

async function savePaymentType() {
  saving.value = true;
  formErrors.value = {};

  try {
    if (isEditMode.value) {
      await api.put(`/payment-types/${editingId.value}`, form.value);
    } else {
      await api.post('/payment-types', form.value);
    }

    await loadPaymentTypes();
    closeModal();
  } catch (err) {
    if (err.response?.data?.errors) {
      formErrors.value = err.response.data.errors;
    } else {
      alert(err.response?.data?.message || 'Ошибка при сохранении типа платежа');
    }
  } finally {
    saving.value = false;
  }
}

async function deletePaymentType(id) {
  if (!confirm('Вы уверены, что хотите удалить этот тип платежа?')) {
    return;
  }

  try {
    await api.delete(`/payment-types/${id}`);
    await loadPaymentTypes();
  } catch (err) {
    alert(err.response?.data?.message || 'Ошибка при удалении типа платежа');
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

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
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

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-action {
  padding: 6px 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.2s;
  background: transparent;
}

.btn-action:hover {
  transform: scale(1.1);
}

.btn-edit:hover {
  background: #e3f2fd;
}

.btn-delete:hover {
  background: #ffebee;
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
  max-width: 700px;
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
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 20px;
  margin-top: 20px;
  border-top: 1px solid #e0e0e0;
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

.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.2s;
  outline: none;
}

.form-input:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error {
  border-color: #e74c3c;
}

.error-message {
  display: block;
  margin-top: 6px;
  color: #e74c3c;
  font-size: 13px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
</style>
