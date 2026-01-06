<template>
  <div class="incashes-page">
    <el-card class="header-card">
      <div class="page-header">
        <h2>Инкассация</h2>
        <el-button type="primary" :icon="Plus" @click="showCreateDialog = true">
          Создать инкассацию
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
            @change="loadIncashes"
          />
        </el-form-item>
        <el-form-item>
          <el-button :icon="Refresh" @click="resetFilters">Сбросить</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <el-card class="table-card">
      <el-table
        v-loading="loading"
        :data="incashes"
        stripe
        style="width: 100%"
        :default-sort="{ prop: 'date', order: 'descending' }"
        show-summary
        :summary-method="getSummaries"
      >
        <el-table-column prop="date" label="Дата" width="120" />
        <el-table-column prop="time" label="Время" width="100" />
        <el-table-column label="Наличные (UZS)" width="160" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.cash_uzs) }}
          </template>
        </el-table-column>
        <el-table-column label="Безнал (UZS)" width="160" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.cashless_uzs) }}
          </template>
        </el-table-column>
        <el-table-column label="Доллары" width="130" align="right">
          <template #default="scope">
            ${{ formatCurrency(scope.row.usd) }}
          </template>
        </el-table-column>
        <el-table-column label="Итого (UZS)" min-width="150" align="right">
          <template #default="scope">
            <strong>{{ formatCurrency(scope.row.total) }} сўм</strong>
          </template>
        </el-table-column>
        <el-table-column label="Кассир" min-width="150">
          <template #default="scope">
            {{ scope.row.cashier?.full_name }}
          </template>
        </el-table-column>
        <el-table-column label="Действия" width="100" fixed="right">
          <template #default="scope">
            <el-button
              type="primary"
              :icon="View"
              circle
              size="small"
              @click="viewIncash(scope.row)"
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
          @current-change="loadIncashes"
          @size-change="loadIncashes"
        />
      </div>
    </el-card>

    <!-- Диалог создания инкассации -->
    <el-dialog
      v-model="showCreateDialog"
      title="Создать инкассацию"
      width="600px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="createFormRef"
        :model="createForm"
        :rules="createRules"
        label-width="180px"
      >
        <el-form-item label="Наличные (UZS)" prop="cash_uzs">
          <el-input-number
            v-model="createForm.cash_uzs"
            :min="0"
            :precision="2"
            :step="10000"
            @change="calculateTotal"
            style="width: 100%"
          >
            <template #suffix>сўм</template>
          </el-input-number>
        </el-form-item>

        <el-form-item label="Безналичные (UZS)" prop="cashless_uzs">
          <el-input-number
            v-model="createForm.cashless_uzs"
            :min="0"
            :precision="2"
            :step="10000"
            @change="calculateTotal"
            style="width: 100%"
          >
            <template #suffix>сўм</template>
          </el-input-number>
        </el-form-item>

        <el-form-item label="Доллары (USD)" prop="usd">
          <el-input-number
            v-model="createForm.usd"
            :min="0"
            :precision="2"
            :step="100"
            @change="calculateTotal"
            style="width: 100%"
          >
            <template #prefix>$</template>
          </el-input-number>
        </el-form-item>

        <el-divider />

        <el-form-item label="Итого (UZS)">
          <el-input-number
            v-model="calculatedTotal"
            disabled
            :precision="2"
            style="width: 100%"
          >
            <template #suffix>сўм</template>
          </el-input-number>
          <el-text type="info" size="small" v-if="currentRate && createForm.usd > 0">
            USD конвертировано по курсу: {{ formatCurrency(currentRate.sell_rate) }}
          </el-text>
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="showCreateDialog = false">Отмена</el-button>
        <el-button type="primary" :loading="creating" @click="createIncash">
          Создать
        </el-button>
      </template>
    </el-dialog>

    <!-- Диалог просмотра инкассации -->
    <el-dialog v-model="showViewDialog" title="Детали инкассации" width="500px">
      <el-descriptions :column="1" border v-if="selectedIncash">
        <el-descriptions-item label="Дата">
          {{ selectedIncash.date }}
        </el-descriptions-item>
        <el-descriptions-item label="Время">
          {{ selectedIncash.time }}
        </el-descriptions-item>
        <el-descriptions-item label="Наличные (UZS)">
          {{ formatCurrency(selectedIncash.cash_uzs) }} сўм
        </el-descriptions-item>
        <el-descriptions-item label="Безналичные (UZS)">
          {{ formatCurrency(selectedIncash.cashless_uzs) }} сўм
        </el-descriptions-item>
        <el-descriptions-item label="Доллары">
          ${{ formatCurrency(selectedIncash.usd) }}
        </el-descriptions-item>
        <el-descriptions-item label="Итого (UZS)">
          <strong>{{ formatCurrency(selectedIncash.total) }} сўм</strong>
        </el-descriptions-item>
        <el-descriptions-item label="Кассир">
          {{ selectedIncash.cashier?.full_name }}
        </el-descriptions-item>
      </el-descriptions>

      <template #footer>
        <el-button @click="showViewDialog = false">Закрыть</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import { Plus, Refresh, View } from '@element-plus/icons-vue';
import api from '@/services/api';

const loading = ref(false);
const creating = ref(false);
const incashes = ref([]);
const currentRate = ref(null);
const currentPage = ref(1);
const pageSize = ref(20);
const total = ref(0);

const showCreateDialog = ref(false);
const showViewDialog = ref(false);
const selectedIncash = ref(null);
const createFormRef = ref(null);

const filters = reactive({
  date: null,
});

const createForm = reactive({
  cash_uzs: 0,
  cashless_uzs: 0,
  usd: 0,
});

const calculatedTotal = ref(0);

const createRules = {
  cash_uzs: [
    { required: true, message: 'Введите сумму', trigger: 'change' },
    { type: 'number', min: 0, message: 'Сумма должна быть >= 0', trigger: 'change' },
  ],
  cashless_uzs: [
    { required: true, message: 'Введите сумму', trigger: 'change' },
    { type: 'number', min: 0, message: 'Сумма должна быть >= 0', trigger: 'change' },
  ],
  usd: [
    { required: true, message: 'Введите сумму', trigger: 'change' },
    { type: 'number', min: 0, message: 'Сумма должна быть >= 0', trigger: 'change' },
  ],
};

const loadIncashes = async () => {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: pageSize.value,
      ...filters,
    };

    const response = await api.get('/incashes', { params });

    if (response.data.success) {
      incashes.value = response.data.data;
      total.value = response.data.meta.total;
    }
  } catch (error) {
    ElMessage.error('Ошибка при загрузке инкассаций: ' + error.message);
  } finally {
    loading.value = false;
  }
};

const loadCurrentRate = async () => {
  try {
    const response = await api.get('/rates');
    if (response.data.success && response.data.data.length > 0) {
      currentRate.value = response.data.data.find(r => r.is_active) || response.data.data[0];
    }
  } catch (error) {
    console.error('Ошибка при загрузке курса:', error);
  }
};

const calculateTotal = () => {
  let total = createForm.cash_uzs + createForm.cashless_uzs;

  if (createForm.usd > 0 && currentRate.value) {
    const usdInUzs = createForm.usd * parseFloat(currentRate.value.sell_rate);
    total += usdInUzs;
  }

  calculatedTotal.value = total;
};

const createIncash = async () => {
  if (!createFormRef.value) return;

  await createFormRef.value.validate(async (valid) => {
    if (!valid) return;

    if (createForm.cash_uzs === 0 && createForm.cashless_uzs === 0 && createForm.usd === 0) {
      ElMessage.warning('Заполните хотя бы одно поле');
      return;
    }

    creating.value = true;
    try {
      const response = await api.post('/incashes', createForm);

      if (response.data.success) {
        ElMessage.success(response.data.message || 'Инкассация успешно создана');
        showCreateDialog.value = false;
        resetCreateForm();
        await loadIncashes();
      }
    } catch (error) {
      ElMessage.error(error.response?.data?.message || 'Ошибка при создании инкассации');
    } finally {
      creating.value = false;
    }
  });
};

const viewIncash = (incash) => {
  selectedIncash.value = incash;
  showViewDialog.value = true;
};

const resetFilters = () => {
  filters.date = null;
  loadIncashes();
};

const resetCreateForm = () => {
  Object.assign(createForm, {
    cash_uzs: 0,
    cashless_uzs: 0,
    usd: 0,
  });
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

const getSummaries = (param) => {
  const { columns, data } = param;
  const sums = [];

  columns.forEach((column, index) => {
    if (index === 0) {
      sums[index] = 'Итого';
      return;
    }

    const values = data.map(item => {
      if (column.property === 'cash_uzs') return Number(item.cash_uzs);
      if (column.property === 'cashless_uzs') return Number(item.cashless_uzs);
      if (column.property === 'usd') return Number(item.usd);
      if (column.property === 'total') return Number(item.total);
      return 0;
    });

    if (column.property && ['cash_uzs', 'cashless_uzs', 'usd', 'total'].includes(column.property)) {
      const sum = values.reduce((prev, curr) => prev + curr, 0);
      sums[index] = column.property === 'usd'
        ? `$${formatCurrency(sum)}`
        : formatCurrency(sum);
    } else {
      sums[index] = '';
    }
  });

  return sums;
};

onMounted(() => {
  loadIncashes();
  loadCurrentRate();
});
</script>

<style scoped>
.incashes-page {
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
</style>
