<template>
  <div class="payments-page">
    <el-card class="header-card">
      <div class="page-header">
        <h2>Платежи</h2>
        <el-button type="primary" :icon="Plus" @click="showCreateDialog = true">
          Создать платеж
        </el-button>
      </div>
    </el-card>

    <el-card class="filters-card">
      <el-form :inline="true" :model="filters">
        <el-form-item label="Дата">
          <el-date-picker
            v-model="filters.date"
            type="date"
            placeholder="Выберите дату"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            @change="loadPayments"
          />
        </el-form-item>
        <el-form-item label="Статус">
          <el-select v-model="filters.status" placeholder="Все" clearable @change="loadPayments">
            <el-option label="Ожидание" value="pending" />
            <el-option label="Подтверждено" value="confirmed" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button :icon="Refresh" @click="resetFilters">Сбросить</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <el-card class="table-card">
      <el-table
        v-loading="loading"
        :data="payments"
        stripe
        style="width: 100%"
        :default-sort="{ prop: 'date', order: 'descending' }"
      >
        <el-table-column prop="list_number" label="№ Списка" width="100" />
        <el-table-column prop="date" label="Дата" width="120" />
        <el-table-column prop="time" label="Время" width="100" />
        <el-table-column label="Тип платежа" width="200">
          <template #default="scope">
            {{ scope.row.payment_type?.name }}
          </template>
        </el-table-column>
        <el-table-column prop="payer_name" label="Плательщик" min-width="150" />
        <el-table-column label="Сумма" width="120" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.amount) }}
          </template>
        </el-table-column>
        <el-table-column label="Комиссия" width="100" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.commission) }}
          </template>
        </el-table-column>
        <el-table-column label="Итого" width="120" align="right">
          <template #default="scope">
            <strong>{{ formatCurrency(scope.row.total) }}</strong>
          </template>
        </el-table-column>
        <el-table-column label="Статус" width="130">
          <template #default="scope">
            <el-tag :type="scope.row.status === 'confirmed' ? 'success' : 'warning'">
              {{ scope.row.status === 'confirmed' ? 'Подтверждено' : 'Ожидание' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Действия" width="100" fixed="right">
          <template #default="scope">
            <el-button
              type="primary"
              :icon="View"
              circle
              size="small"
              @click="viewPayment(scope.row)"
            />
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination">
        <el-pagination
          v-model:current-page="currentPage"
          v-model:page-size="pageSize"
          :total="total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next"
          @current-change="loadPayments"
          @size-change="loadPayments"
        />
      </div>
    </el-card>

    <!-- Диалог создания платежа -->
    <el-dialog
      v-model="showCreateDialog"
      title="Создать платеж"
      width="700px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="createFormRef"
        :model="createForm"
        :rules="createRules"
        label-width="150px"
      >
        <el-form-item label="Тип платежа" prop="payment_type_id">
          <el-select
            v-model="createForm.payment_type_id"
            placeholder="Выберите тип"
            filterable
            @change="onPaymentTypeChange"
          >
            <el-option
              v-for="type in paymentTypes"
              :key="type.id"
              :label="type.name"
              :value="type.id"
            >
              <div class="payment-type-option">
                <span>{{ type.name }}</span>
                <el-tag size="small" type="info">{{ type.organization }}</el-tag>
              </div>
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="№ Списка" prop="list_number">
          <el-input v-model="createForm.list_number" placeholder="Введите номер списка" />
        </el-form-item>

        <el-form-item label="Плательщик" prop="payer_name">
          <el-input v-model="createForm.payer_name" placeholder="ФИО плательщика" />
        </el-form-item>

        <el-form-item label="Назначение" prop="purpose">
          <el-input
            v-model="createForm.purpose"
            type="textarea"
            :rows="2"
            placeholder="Назначение платежа"
          />
        </el-form-item>

        <el-form-item label="Сумма" prop="amount">
          <el-input-number
            v-model="createForm.amount"
            :min="0"
            :precision="2"
            :step="1000"
            @change="calculateCommission"
          />
        </el-form-item>

        <el-form-item label="Способ оплаты" prop="payment_method">
          <el-radio-group v-model="createForm.payment_method">
            <el-radio value="cash">Наличные</el-radio>
            <el-radio value="card">Карта</el-radio>
            <el-radio value="transfer">Перевод</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="Платежная система" v-if="createForm.payment_method === 'card'">
          <el-select v-model="createForm.payment_system" placeholder="Выберите систему">
            <el-option label="UzCard" value="uzcard" />
            <el-option label="Humo" value="humo" />
            <el-option label="Visa" value="visa" />
            <el-option label="MasterCard" value="mastercard" />
          </el-select>
        </el-form-item>

        <el-form-item label="Город">
          <el-input v-model="createForm.city" placeholder="Название города" />
        </el-form-item>

        <el-form-item label="Регион">
          <el-input v-model="createForm.region" placeholder="Название региона" />
        </el-form-item>

        <el-divider />

        <el-form-item label="Комиссия">
          <el-input-number v-model="calculatedCommission" disabled :precision="2" />
        </el-form-item>

        <el-form-item label="Итого к оплате">
          <el-input-number v-model="calculatedTotal" disabled :precision="2" />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="showCreateDialog = false">Отмена</el-button>
        <el-button type="primary" :loading="creating" @click="createPayment">
          Создать
        </el-button>
      </template>
    </el-dialog>

    <!-- Диалог просмотра платежа -->
    <el-dialog v-model="showViewDialog" title="Детали платежа" width="600px">
      <el-descriptions :column="2" border v-if="selectedPayment">
        <el-descriptions-item label="№ Списка">
          {{ selectedPayment.list_number }}
        </el-descriptions-item>
        <el-descriptions-item label="Случайный №">
          {{ selectedPayment.random_number }}
        </el-descriptions-item>
        <el-descriptions-item label="Дата">
          {{ selectedPayment.date }}
        </el-descriptions-item>
        <el-descriptions-item label="Время">
          {{ selectedPayment.time }}
        </el-descriptions-item>
        <el-descriptions-item label="Тип платежа" :span="2">
          {{ selectedPayment.payment_type?.name }}
        </el-descriptions-item>
        <el-descriptions-item label="Организация" :span="2">
          {{ selectedPayment.payment_type?.organization }}
        </el-descriptions-item>
        <el-descriptions-item label="Плательщик" :span="2">
          {{ selectedPayment.payer_name }}
        </el-descriptions-item>
        <el-descriptions-item label="Назначение" :span="2">
          {{ selectedPayment.purpose }}
        </el-descriptions-item>
        <el-descriptions-item label="Сумма">
          {{ formatCurrency(selectedPayment.amount) }}
        </el-descriptions-item>
        <el-descriptions-item label="Комиссия">
          {{ formatCurrency(selectedPayment.commission) }}
        </el-descriptions-item>
        <el-descriptions-item label="Итого">
          <strong>{{ formatCurrency(selectedPayment.total) }}</strong>
        </el-descriptions-item>
        <el-descriptions-item label="Способ оплаты">
          {{ getPaymentMethodLabel(selectedPayment.payment_method) }}
        </el-descriptions-item>
        <el-descriptions-item label="Платежная система" v-if="selectedPayment.payment_system">
          {{ selectedPayment.payment_system?.toUpperCase() }}
        </el-descriptions-item>
        <el-descriptions-item label="Город" v-if="selectedPayment.city">
          {{ selectedPayment.city }}
        </el-descriptions-item>
        <el-descriptions-item label="Регион" v-if="selectedPayment.region">
          {{ selectedPayment.region }}
        </el-descriptions-item>
        <el-descriptions-item label="Кассир" :span="2">
          {{ selectedPayment.cashier?.full_name }}
        </el-descriptions-item>
        <el-descriptions-item label="Статус" :span="2">
          <el-tag :type="selectedPayment.status === 'confirmed' ? 'success' : 'warning'">
            {{ selectedPayment.status === 'confirmed' ? 'Подтверждено' : 'Ожидание' }}
          </el-tag>
        </el-descriptions-item>
      </el-descriptions>

      <template #footer>
        <el-button @click="showViewDialog = false">Закрыть</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import { Plus, Refresh, View } from '@element-plus/icons-vue';
import api from '@/services/api';

const loading = ref(false);
const creating = ref(false);
const payments = ref([]);
const paymentTypes = ref([]);
const currentPage = ref(1);
const pageSize = ref(20);
const total = ref(0);

const showCreateDialog = ref(false);
const showViewDialog = ref(false);
const selectedPayment = ref(null);
const createFormRef = ref(null);

const filters = reactive({
  date: null,
  status: null,
});

const createForm = reactive({
  payment_type_id: null,
  list_number: '',
  payer_name: '',
  purpose: '',
  amount: 0,
  payment_method: 'cash',
  payment_system: null,
  currency: 'UZS',
  city: '',
  region: '',
});

const calculatedCommission = ref(0);
const calculatedTotal = ref(0);

const createRules = {
  payment_type_id: [{ required: true, message: 'Выберите тип платежа', trigger: 'change' }],
  list_number: [{ required: true, message: 'Введите номер списка', trigger: 'blur' }],
  payer_name: [{ required: true, message: 'Введите ФИО плательщика', trigger: 'blur' }],
  purpose: [{ required: true, message: 'Введите назначение платежа', trigger: 'blur' }],
  amount: [
    { required: true, message: 'Введите сумму', trigger: 'change' },
    { type: 'number', min: 0.01, message: 'Сумма должна быть больше 0', trigger: 'change' },
  ],
  payment_method: [{ required: true, message: 'Выберите способ оплаты', trigger: 'change' }],
};

const loadPayments = async () => {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: pageSize.value,
      ...filters,
    };

    const response = await api.get('/payments', { params });

    if (response.data.success) {
      payments.value = response.data.data;
      total.value = response.data.meta.total;
    }
  } catch (error) {
    ElMessage.error('Ошибка при загрузке платежей: ' + error.message);
  } finally {
    loading.value = false;
  }
};

const loadPaymentTypes = async () => {
  try {
    const response = await api.get('/payment-types');
    if (response.data.success) {
      paymentTypes.value = response.data.data;
    }
  } catch (error) {
    ElMessage.error('Ошибка при загрузке типов платежей');
  }
};

const onPaymentTypeChange = () => {
  calculateCommission();
};

const calculateCommission = () => {
  if (!createForm.payment_type_id || !createForm.amount) {
    calculatedCommission.value = 0;
    calculatedTotal.value = createForm.amount || 0;
    return;
  }

  const paymentType = paymentTypes.value.find(t => t.id === createForm.payment_type_id);
  if (!paymentType) return;

  const percentageCommission = (createForm.amount * parseFloat(paymentType.commission_percentage)) / 100;
  const fixedCommission = parseFloat(paymentType.commission_fixed);
  calculatedCommission.value = percentageCommission + fixedCommission;
  calculatedTotal.value = createForm.amount + calculatedCommission.value;
};

const createPayment = async () => {
  if (!createFormRef.value) return;

  await createFormRef.value.validate(async (valid) => {
    if (!valid) return;

    creating.value = true;
    try {
      const response = await api.post('/payments', createForm);

      if (response.data.success) {
        ElMessage.success(response.data.message || 'Платеж успешно создан');
        showCreateDialog.value = false;
        resetCreateForm();
        await loadPayments();
      }
    } catch (error) {
      ElMessage.error(error.response?.data?.message || 'Ошибка при создании платежа');
    } finally {
      creating.value = false;
    }
  });
};

const viewPayment = (payment) => {
  selectedPayment.value = payment;
  showViewDialog.value = true;
};

const resetFilters = () => {
  filters.date = null;
  filters.status = null;
  loadPayments();
};

const resetCreateForm = () => {
  Object.assign(createForm, {
    payment_type_id: null,
    list_number: '',
    payer_name: '',
    purpose: '',
    amount: 0,
    payment_method: 'cash',
    payment_system: null,
    currency: 'UZS',
    city: '',
    region: '',
  });
  calculatedCommission.value = 0;
  calculatedTotal.value = 0;
  createFormRef.value?.resetFields();
};

const formatCurrency = (value) => {
  if (!value) return '0.00';
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value);
};

const getPaymentMethodLabel = (method) => {
  const labels = {
    cash: 'Наличные',
    card: 'Карта',
    transfer: 'Перевод',
  };
  return labels[method] || method;
};

onMounted(() => {
  loadPayments();
  loadPaymentTypes();
});
</script>

<style scoped>
.payments-page {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.header-card {
  margin-bottom: 0;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-header h2 {
  margin: 0;
  font-size: 24px;
  color: #303133;
}

.filters-card {
  margin-bottom: 0;
}

.table-card {
  flex: 1;
}

.pagination {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
}

.payment-type-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}
</style>
