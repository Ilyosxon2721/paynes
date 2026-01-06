<template>
  <div class="credits-page">
    <el-card class="header-card">
      <div class="page-header">
        <h2>Кредиты</h2>
        <el-button type="primary" :icon="Plus" @click="showCreateDialog = true">
          Создать кредит
        </el-button>
      </div>
    </el-card>

    <el-card class="filters-card">
      <el-form :inline="true" :model="filters">
        <el-form-item label="Статус">
          <el-select v-model="filters.status" placeholder="Все" clearable @change="loadCredits">
            <el-option label="Ожидание" value="pending" />
            <el-option label="Подтверждено" value="confirmed" />
            <el-option label="Погашено" value="repaid" />
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
        :data="credits"
        stripe
        style="width: 100%"
        :default-sort="{ prop: 'date', order: 'descending' }"
      >
        <el-table-column prop="date" label="Дата" width="120" />
        <el-table-column prop="time" label="Время" width="100" />
        <el-table-column prop="recipient" label="Получатель" min-width="180" />
        <el-table-column prop="account_number" label="Номер счета" width="150" />
        <el-table-column prop="branch" label="Филиал" min-width="150" />
        <el-table-column label="Дебет" width="130" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.debit) }}
          </template>
        </el-table-column>
        <el-table-column label="Кредит" width="130" align="right">
          <template #default="scope">
            <strong>{{ formatCurrency(scope.row.credit) }}</strong>
          </template>
        </el-table-column>
        <el-table-column label="Остаток долга" width="140" align="right">
          <template #default="scope">
            <el-text v-if="scope.row.status === 'confirmed' && scope.row.remaining_balance > 0" type="danger" size="large">
              <strong>{{ formatCurrency(scope.row.remaining_balance) }}</strong>
            </el-text>
            <el-text v-else-if="scope.row.remaining_balance === 0 && scope.row.status === 'confirmed'" type="success">
              Погашено
            </el-text>
            <el-text v-else type="info">
              —
            </el-text>
          </template>
        </el-table-column>
        <el-table-column label="Статус" width="130">
          <template #default="scope">
            <el-tag :type="getStatusType(scope.row.status)">
              {{ getStatusLabel(scope.row.status) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Действия" width="150" fixed="right">
          <template #default="scope">
            <el-button
              type="primary"
              :icon="View"
              circle
              size="small"
              @click="viewCredit(scope.row)"
            />
            <el-button
              v-if="scope.row.status === 'confirmed' && scope.row.remaining_balance > 0"
              type="success"
              :icon="Money"
              circle
              size="small"
              @click="showRepayDialog(scope.row)"
              title="Погасить"
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
          @current-change="loadCredits"
          @size-change="loadCredits"
        />
      </div>
    </el-card>

    <!-- Диалог создания кредита -->
    <el-dialog
      v-model="showCreateDialog"
      title="Создать кредит"
      width="600px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="createFormRef"
        :model="createForm"
        :rules="createRules"
        label-width="150px"
      >
        <el-form-item label="Получатель" prop="recipient">
          <el-input v-model="createForm.recipient" placeholder="ФИО получателя" />
        </el-form-item>

        <el-form-item label="Филиал" prop="branch">
          <el-input v-model="createForm.branch" placeholder="Название филиала" />
        </el-form-item>

        <el-form-item label="Дебет" prop="debit">
          <el-input-number
            v-model="createForm.debit"
            :min="0"
            :precision="2"
            :step="1000"
            style="width: 100%"
          />
        </el-form-item>

        <el-form-item label="Кредит" prop="credit">
          <el-input-number
            v-model="createForm.credit"
            :min="0"
            :precision="2"
            :step="1000"
            style="width: 100%"
          />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="showCreateDialog = false">Отмена</el-button>
        <el-button type="primary" :loading="creating" @click="createCredit">
          Создать
        </el-button>
      </template>
    </el-dialog>

    <!-- Диалог погашения кредита -->
    <el-dialog
      v-model="showRepaymentDialog"
      title="Погашение кредита"
      width="500px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="repayFormRef"
        :model="repayForm"
        :rules="repayRules"
        label-width="150px"
        v-if="selectedCredit"
      >
        <el-descriptions :column="1" border class="mb-4">
          <el-descriptions-item label="Получатель">
            {{ selectedCredit.recipient }}
          </el-descriptions-item>
          <el-descriptions-item label="Сумма кредита">
            {{ formatCurrency(selectedCredit.debit) }} сўм
          </el-descriptions-item>
          <el-descriptions-item label="Всего погашено">
            <el-text type="success">{{ formatCurrency(selectedCredit.total_repaid || 0) }} сўм</el-text>
          </el-descriptions-item>
          <el-descriptions-item label="Остаток долга">
            <el-text type="danger" size="large">
              <strong>{{ formatCurrency(selectedCredit.remaining_balance) }} сўм</strong>
            </el-text>
          </el-descriptions-item>
        </el-descriptions>

        <el-form-item label="Сумма погашения" prop="amount">
          <el-input-number
            v-model="repayForm.amount"
            :min="0.01"
            :max="parseFloat(selectedCredit.remaining_balance)"
            :precision="2"
            :step="1000"
            style="width: 100%"
          />
          <el-text type="info" size="small">
            Максимум: {{ formatCurrency(selectedCredit.remaining_balance) }} сўм
          </el-text>
        </el-form-item>

        <el-form-item label="Примечание">
          <el-input
            v-model="repayForm.note"
            type="textarea"
            :rows="2"
            placeholder="Дополнительная информация (необязательно)"
          />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="showRepaymentDialog = false">Отмена</el-button>
        <el-button type="success" :loading="repaying" @click="repayCredit">
          Погасить
        </el-button>
      </template>
    </el-dialog>

    <!-- Диалог просмотра кредита -->
    <el-dialog v-model="showViewDialog" title="Детали кредита" width="700px">
      <div v-if="selectedCredit">
        <el-descriptions :column="2" border class="mb-4">
          <el-descriptions-item label="Дата">
            {{ selectedCredit.date }}
          </el-descriptions-item>
          <el-descriptions-item label="Время">
            {{ selectedCredit.time }}
          </el-descriptions-item>
          <el-descriptions-item label="Получатель" :span="2">
            {{ selectedCredit.recipient }}
          </el-descriptions-item>
          <el-descriptions-item label="Номер счета" :span="2">
            {{ selectedCredit.account_number || 'Не указан' }}
          </el-descriptions-item>
          <el-descriptions-item label="Филиал" :span="2">
            {{ selectedCredit.branch }}
          </el-descriptions-item>
          <el-descriptions-item label="Дебет (выдано)">
            <el-text type="warning">{{ formatCurrency(selectedCredit.debit) }} сўм</el-text>
          </el-descriptions-item>
          <el-descriptions-item label="Кредит (получено)">
            <el-text type="success">{{ formatCurrency(selectedCredit.credit) }} сўм</el-text>
          </el-descriptions-item>
          <el-descriptions-item label="Всего погашено" v-if="selectedCredit.status === 'confirmed'">
            <el-text type="success">
              <strong>{{ formatCurrency(selectedCredit.total_repaid || 0) }} сўм</strong>
            </el-text>
          </el-descriptions-item>
          <el-descriptions-item label="Остаток долга" v-if="selectedCredit.status === 'confirmed'">
            <el-text :type="selectedCredit.remaining_balance > 0 ? 'danger' : 'success'">
              <strong>{{ formatCurrency(selectedCredit.remaining_balance || 0) }} сўм</strong>
            </el-text>
          </el-descriptions-item>
          <el-descriptions-item label="Подтвердил" v-if="selectedCredit.confirmed_by">
            {{ selectedCredit.confirmed_by }}
          </el-descriptions-item>
          <el-descriptions-item label="Статус">
            <el-tag :type="getStatusType(selectedCredit.status)">
              {{ getStatusLabel(selectedCredit.status) }}
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="Кассир" :span="2">
            {{ selectedCredit.cashier?.full_name }}
          </el-descriptions-item>
        </el-descriptions>

        <!-- История погашений -->
        <div v-if="selectedCredit.repayments && selectedCredit.repayments.length > 0" class="repayments-section">
          <el-divider>История погашений</el-divider>
          <el-table :data="selectedCredit.repayments" stripe size="small">
            <el-table-column prop="repayment_date" label="Дата" width="110" />
            <el-table-column prop="repayment_time" label="Время" width="90" />
            <el-table-column label="Сумма" width="130" align="right">
              <template #default="scope">
                <el-text type="success">
                  <strong>{{ formatCurrency(scope.row.amount) }} сўм</strong>
                </el-text>
              </template>
            </el-table-column>
            <el-table-column prop="notes" label="Примечание" min-width="150">
              <template #default="scope">
                {{ scope.row.notes || '—' }}
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>

      <template #footer>
        <el-button @click="showViewDialog = false">Закрыть</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import { Plus, Refresh, View, Money } from '@element-plus/icons-vue';
import api from '@/services/api';

const loading = ref(false);
const creating = ref(false);
const repaying = ref(false);
const credits = ref([]);
const currentPage = ref(1);
const pageSize = ref(20);
const total = ref(0);

const showCreateDialog = ref(false);
const showViewDialog = ref(false);
const showRepaymentDialog = ref(false);
const selectedCredit = ref(null);
const createFormRef = ref(null);
const repayFormRef = ref(null);

const filters = reactive({
  status: null,
});

const createForm = reactive({
  recipient: '',
  branch: '',
  debit: 0,
  credit: 0,
});

const repayForm = reactive({
  amount: 0,
  note: '',
});

const createRules = {
  recipient: [{ required: true, message: 'Введите получателя', trigger: 'blur' }],
  branch: [{ required: true, message: 'Введите филиал', trigger: 'blur' }],
  debit: [
    { required: true, message: 'Введите дебет', trigger: 'change' },
    { type: 'number', min: 0, message: 'Дебет должен быть >= 0', trigger: 'change' },
  ],
  credit: [
    { required: true, message: 'Введите кредит', trigger: 'change' },
    { type: 'number', min: 0.01, message: 'Кредит должен быть > 0', trigger: 'change' },
  ],
};

const repayRules = {
  amount: [
    { required: true, message: 'Введите сумму', trigger: 'change' },
    { type: 'number', min: 0.01, message: 'Сумма должна быть > 0', trigger: 'change' },
  ],
};

const loadCredits = async () => {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: pageSize.value,
      ...filters,
    };

    const response = await api.get('/credits', { params });

    if (response.data.success) {
      credits.value = response.data.data;
      total.value = response.data.meta.total;
    }
  } catch (error) {
    ElMessage.error('Ошибка при загрузке кредитов: ' + error.message);
  } finally {
    loading.value = false;
  }
};

const createCredit = async () => {
  if (!createFormRef.value) return;

  await createFormRef.value.validate(async (valid) => {
    if (!valid) return;

    creating.value = true;
    try {
      const response = await api.post('/credits', createForm);

      if (response.data.success) {
        ElMessage.success(response.data.message || 'Кредит успешно создан');
        showCreateDialog.value = false;
        resetCreateForm();
        await loadCredits();
      }
    } catch (error) {
      ElMessage.error(error.response?.data?.message || 'Ошибка при создании кредита');
    } finally {
      creating.value = false;
    }
  });
};

const showRepayDialog = (credit) => {
  selectedCredit.value = credit;
  repayForm.amount = parseFloat(credit.remaining_balance || credit.debit);
  repayForm.note = '';
  showRepaymentDialog.value = true;
};

const repayCredit = async () => {
  if (!repayFormRef.value || !selectedCredit.value) return;

  await repayFormRef.value.validate(async (valid) => {
    if (!valid) return;

    repaying.value = true;
    try {
      const response = await axios.post(`/api/credits/${selectedCredit.value.id}/repay`, repayForm);

      if (response.data.success) {
        ElMessage.success(response.data.message || 'Кредит успешно погашен');
        showRepaymentDialog.value = false;
        selectedCredit.value = null;
        await loadCredits();
      }
    } catch (error) {
      ElMessage.error(error.response?.data?.message || 'Ошибка при погашении кредита');
    } finally {
      repaying.value = false;
    }
  });
};

const viewCredit = async (credit) => {
  // Load full credit details with repayments
  try {
    const response = await axios.get(`/api/credits/${credit.id}`);
    if (response.data.success) {
      selectedCredit.value = response.data.data;
      showViewDialog.value = true;
    }
  } catch (error) {
    ElMessage.error('Ошибка при загрузке деталей кредита');
  }
};

const resetFilters = () => {
  filters.status = null;
  loadCredits();
};

const resetCreateForm = () => {
  Object.assign(createForm, {
    recipient: '',
    branch: '',
    debit: 0,
    credit: 0,
  });
  createFormRef.value?.resetFields();
};

const formatCurrency = (value) => {
  if (!value) return '0.00';
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value);
};

const getStatusType = (status) => {
  const types = {
    pending: 'warning',
    confirmed: 'info',
    repaid: 'success',
  };
  return types[status] || 'info';
};

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Ожидание',
    confirmed: 'Подтверждено',
    repaid: 'Погашено',
  };
  return labels[status] || status;
};

onMounted(() => {
  loadCredits();
});
</script>

<style scoped>
.credits-page {
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

.mb-4 {
  margin-bottom: 20px;
}

.repayments-section {
  margin-top: 24px;
}

.repayments-section :deep(.el-divider__text) {
  font-weight: 600;
  font-size: 14px;
  color: #303133;
}
</style>
