<template>
  <div class="shift-controls">
    <!-- Индикатор статуса смены -->
    <div v-if="currentShift" class="shift-status shift-status--open">
      <div class="shift-status__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
      </div>
      <div class="shift-status__info">
        <div class="shift-status__label">Смена открыта</div>
        <div class="shift-status__time">{{ formatShiftTime(currentShift.shift_date, currentShift.opened_at) }}</div>
      </div>
      <button @click="showCloseModal = true" class="btn btn--danger btn--sm">
        Закрыть смену
      </button>
    </div>

    <div v-else class="shift-status shift-status--closed">
      <div class="shift-status__icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
        </svg>
      </div>
      <div class="shift-status__info">
        <div class="shift-status__label">Смена закрыта</div>
        <div class="shift-status__subtitle">Откройте смену для работы</div>
      </div>
      <button @click="showOpenModal = true" class="btn btn--primary btn--sm">
        Открыть смену
      </button>
    </div>

    <!-- Модальное окно открытия смены -->
    <div v-if="showOpenModal" class="modal-overlay" @click.self="showOpenModal = false">
      <div class="modal">
        <div class="modal__header">
          <h3 class="modal__title">Открыть смену</h3>
          <button @click="showOpenModal = false" class="modal__close">&times;</button>
        </div>

        <div class="modal__body">
          <p class="modal__description">
            Укажите начальные балансы кассы. Если оставить поля пустыми, будут использованы балансы из предыдущей смены.
          </p>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Наличные UZS</label>
              <input
                v-model.number="openForm.opening_cash_uzs"
                type="number"
                step="0.01"
                class="form-control"
                placeholder="Автоматически"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Безналичные UZS</label>
              <input
                v-model.number="openForm.opening_cashless_uzs"
                type="number"
                step="0.01"
                class="form-control"
                placeholder="Автоматически"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Карты UZS</label>
              <input
                v-model.number="openForm.opening_card_uzs"
                type="number"
                step="0.01"
                class="form-control"
                placeholder="Автоматически"
              />
            </div>

            <div class="form-group">
              <label class="form-label">P2P UZS</label>
              <input
                v-model.number="openForm.opening_p2p_uzs"
                type="number"
                step="0.01"
                class="form-control"
                placeholder="Автоматически"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Наличные USD</label>
              <input
                v-model.number="openForm.opening_cash_usd"
                type="number"
                step="0.01"
                class="form-control"
                placeholder="Автоматически"
              />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Примечания</label>
            <textarea
              v-model="openForm.opening_notes"
              class="form-control"
              rows="3"
              placeholder="Дополнительные заметки при открытии смены"
            ></textarea>
          </div>

          <div v-if="error" class="alert alert--danger">
            {{ error }}
          </div>
        </div>

        <div class="modal__footer">
          <button @click="showOpenModal = false" class="btn btn--secondary" :disabled="loading">
            Отмена
          </button>
          <button @click="openShift" class="btn btn--primary" :disabled="loading">
            {{ loading ? 'Открытие...' : 'Открыть смену' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Модальное окно закрытия смены -->
    <div v-if="showCloseModal" class="modal-overlay" @click.self="showCloseModal = false">
      <div class="modal">
        <div class="modal__header">
          <h3 class="modal__title">Закрыть смену</h3>
          <button @click="showCloseModal = false" class="modal__close">&times;</button>
        </div>

        <div class="modal__body">
          <div v-if="closeValidation.errors.length > 0" class="alert alert--warning">
            <strong>Невозможно закрыть смену:</strong>
            <ul>
              <li v-for="(error, index) in closeValidation.errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div v-else>
            <p class="modal__description">
              Подтвердите закрытие смены. Итоговые балансы будут рассчитаны автоматически.
            </p>

            <div v-if="closingBalances" class="balances-preview">
              <h4 class="balances-preview__title">Итоговые балансы:</h4>
              <div class="balances-preview__grid">
                <div class="balance-item">
                  <span class="balance-item__label">Наличные UZS:</span>
                  <span class="balance-item__value">{{ formatMoney(closingBalances.cash_uzs) }}</span>
                </div>
                <div class="balance-item">
                  <span class="balance-item__label">Безналичные UZS:</span>
                  <span class="balance-item__value">{{ formatMoney(closingBalances.cashless_uzs) }}</span>
                </div>
                <div class="balance-item">
                  <span class="balance-item__label">Карты UZS:</span>
                  <span class="balance-item__value">{{ formatMoney(closingBalances.card_uzs) }}</span>
                </div>
                <div class="balance-item">
                  <span class="balance-item__label">P2P UZS:</span>
                  <span class="balance-item__value">{{ formatMoney(closingBalances.p2p_uzs) }}</span>
                </div>
                <div class="balance-item">
                  <span class="balance-item__label">Наличные USD:</span>
                  <span class="balance-item__value">{{ formatMoney(closingBalances.cash_usd, 'USD') }}</span>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Примечания при закрытии</label>
              <textarea
                v-model="closeForm.closing_notes"
                class="form-control"
                rows="3"
                placeholder="Дополнительные заметки при закрытии смены"
              ></textarea>
            </div>
          </div>

          <div v-if="error" class="alert alert--danger">
            {{ error }}
          </div>
        </div>

        <div class="modal__footer">
          <button @click="showCloseModal = false" class="btn btn--secondary" :disabled="loading">
            Отмена
          </button>
          <button
            v-if="closeValidation.errors.length === 0"
            @click="closeShift"
            class="btn btn--danger"
            :disabled="loading"
          >
            {{ loading ? 'Закрытие...' : 'Закрыть смену' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import api from '@/services/api';

const emit = defineEmits(['shift-opened', 'shift-closed', 'shift-changed']);

const currentShift = ref(null);
const showOpenModal = ref(false);
const showCloseModal = ref(false);
const loading = ref(false);
const error = ref(null);
const closingBalances = ref(null);
const closeValidation = ref({ can_close: true, errors: [] });

const openForm = ref({
  opening_cash_uzs: null,
  opening_cashless_uzs: null,
  opening_card_uzs: null,
  opening_p2p_uzs: null,
  opening_cash_usd: null,
  opening_notes: '',
});

const closeForm = ref({
  closing_notes: '',
});

// Загрузка текущей смены
const loadCurrentShift = async () => {
  try {
    const response = await api.get('/cashier-shifts/current');
    currentShift.value = response.data.data;
    emit('shift-changed', currentShift.value);
  } catch (err) {
    console.error('Ошибка при загрузке текущей смены:', err);
  }
};

// Открытие смены
const openShift = async () => {
  loading.value = true;
  error.value = null;

  try {
    const payload = {};

    // Добавляем только заполненные поля
    if (openForm.value.opening_cash_uzs !== null) payload.opening_cash_uzs = openForm.value.opening_cash_uzs;
    if (openForm.value.opening_cashless_uzs !== null) payload.opening_cashless_uzs = openForm.value.opening_cashless_uzs;
    if (openForm.value.opening_card_uzs !== null) payload.opening_card_uzs = openForm.value.opening_card_uzs;
    if (openForm.value.opening_p2p_uzs !== null) payload.opening_p2p_uzs = openForm.value.opening_p2p_uzs;
    if (openForm.value.opening_cash_usd !== null) payload.opening_cash_usd = openForm.value.opening_cash_usd;
    if (openForm.value.opening_notes) payload.opening_notes = openForm.value.opening_notes;

    const response = await api.post('/cashier-shifts/open', payload);

    currentShift.value = response.data.data;
    showOpenModal.value = false;

    // Сброс формы
    openForm.value = {
      opening_cash_uzs: null,
      opening_cashless_uzs: null,
      opening_card_uzs: null,
      opening_p2p_uzs: null,
      opening_cash_usd: null,
      opening_notes: '',
    };

    emit('shift-opened', currentShift.value);
    emit('shift-changed', currentShift.value);
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка при открытии смены';
  } finally {
    loading.value = false;
  }
};

// Закрытие смены
const closeShift = async () => {
  if (!currentShift.value) return;

  loading.value = true;
  error.value = null;

  try {
    const response = await api.post(`/cashier-shifts/${currentShift.value.id}/close`, closeForm.value);

    const closedShift = response.data.data;
    currentShift.value = null;
    showCloseModal.value = false;

    // Сброс формы
    closeForm.value = { closing_notes: '' };
    closingBalances.value = null;

    emit('shift-closed', closedShift);
    emit('shift-changed', null);
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка при закрытии смены';
  } finally {
    loading.value = false;
  }
};

// Загрузка балансов и проверка возможности закрытия при открытии модального окна
watch(showCloseModal, async (isOpen) => {
  if (isOpen && currentShift.value) {
    try {
      const response = await api.get(`/cashier-shifts/${currentShift.value.id}/report`);
      closingBalances.value = response.data.data.balances;

      // Проверяем возможность закрытия
      const shift = response.data.data.shift;
      if (shift.status === 'open') {
        // Backend проверит при попытке закрытия, но мы можем показать предупреждение
        closeValidation.value = { can_close: true, errors: [] };
      }
    } catch (err) {
      console.error('Ошибка при загрузке данных смены:', err);
    }
  }
});

// Форматирование времени смены
const formatShiftTime = (date, time) => {
  const dateObj = new Date(`${date} ${time}`);
  return dateObj.toLocaleString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

// Форматирование денег
const formatMoney = (amount, currency = 'UZS') => {
  const formatted = new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount || 0);
  return `${formatted} ${currency}`;
};

onMounted(() => {
  loadCurrentShift();
});

// Экспорт для использования в родительских компонентах
defineExpose({
  currentShift: computed(() => currentShift.value),
  reload: loadCurrentShift,
});
</script>

<style scoped>
.shift-controls {
  margin-bottom: 1.5rem;
}

.shift-status {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  background: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.shift-status--open {
  border-left: 4px solid #10b981;
}

.shift-status--closed {
  border-left: 4px solid #ef4444;
}

.shift-status__icon {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.shift-status--open .shift-status__icon {
  background: #d1fae5;
  color: #10b981;
}

.shift-status--closed .shift-status__icon {
  background: #fee2e2;
  color: #ef4444;
}

.shift-status__info {
  flex: 1;
}

.shift-status__label {
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.shift-status__time,
.shift-status__subtitle {
  font-size: 0.875rem;
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
  padding: 1rem;
}

.modal {
  background: white;
  border-radius: 12px;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal__title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}

.modal__close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: background 0.2s;
}

.modal__close:hover {
  background: #f3f4f6;
}

.modal__body {
  padding: 1.5rem;
  overflow-y: auto;
  flex: 1;
}

.modal__description {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.modal__footer {
  display: flex;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  justify-content: flex-end;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: #374151;
  font-size: 0.875rem;
}

.form-control {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn {
  padding: 0.625rem 1.25rem;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn--sm {
  padding: 0.5rem 1rem;
  font-size: 0.8125rem;
}

.btn--primary {
  background: #3b82f6;
  color: white;
}

.btn--primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn--danger {
  background: #ef4444;
  color: white;
}

.btn--danger:hover:not(:disabled) {
  background: #dc2626;
}

.btn--secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn--secondary:hover:not(:disabled) {
  background: #e5e7eb;
}

.alert {
  padding: 1rem;
  border-radius: 6px;
  margin-bottom: 1rem;
}

.alert--danger {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.alert--warning {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
}

.alert ul {
  margin: 0.5rem 0 0 0;
  padding-left: 1.25rem;
}

.balances-preview {
  background: #f9fafb;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.balances-preview__title {
  font-size: 0.875rem;
  font-weight: 600;
  margin: 0 0 1rem 0;
  color: #374151;
}

.balances-preview__grid {
  display: grid;
  gap: 0.75rem;
}

.balance-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  background: white;
  border-radius: 4px;
}

.balance-item__label {
  font-size: 0.875rem;
  color: #6b7280;
}

.balance-item__value {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
}
</style>
