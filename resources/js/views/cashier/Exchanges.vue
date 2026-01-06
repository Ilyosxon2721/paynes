<template>
  <div class="exchanges-page">
    <el-card class="header-card">
      <div class="page-header">
        <h2>Обмен валют</h2>
        <el-button type="primary" :icon="Plus" @click="showCreateDialog = true">
          Создать обмен
        </el-button>
      </div>
    </el-card>

    <el-card class="rate-card">
      <el-descriptions title="Текущий курс USD" :column="2" border v-if="currentRate">
        <el-descriptions-item label="Покупка">
          <el-tag type="success" size="large">
            {{ formatCurrency(currentRate.buy_rate) }} UZS
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="Продажа">
          <el-tag type="warning" size="large">
            {{ formatCurrency(currentRate.sell_rate) }} UZS
          </el-tag>
        </el-descriptions-item>
      </el-descriptions>
      <el-empty v-else description="Курс не установлен" />
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
            @change="loadExchanges"
          />
        </el-form-item>
        <el-form-item label="Тип">
          <el-select v-model="filters.type" placeholder="Все" clearable @change="loadExchanges">
            <el-option label="Покупка" value="buy" />
            <el-option label="Продажа" value="sell" />
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
        :data="exchanges"
        stripe
        style="width: 100%"
        :default-sort="{ prop: 'date', order: 'descending' }"
      >
        <el-table-column prop="date" label="Дата" width="120" />
        <el-table-column prop="time" label="Время" width="100" />
        <el-table-column label="Тип" width="120">
          <template #default="scope">
            <el-tag :type="scope.row.type === 'buy' ? 'success' : 'warning'">
              {{ scope.row.type === 'buy' ? 'Покупка' : 'Продажа' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="USD" width="150" align="right">
          <template #default="scope">
            ${{ formatCurrency(scope.row.usd_amount) }}
          </template>
        </el-table-column>
        <el-table-column label="Курс" width="150" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.rate) }}
          </template>
        </el-table-column>
        <el-table-column label="UZS" min-width="150" align="right">
          <template #default="scope">
            <strong>{{ formatCurrency(scope.row.uzs_amount) }} сўм</strong>
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
              @click="viewExchange(scope.row)"
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
          @current-change="loadExchanges"
          @size-change="loadExchanges"
        />
      </div>
    </el-card>

    <!-- Диалог создания обмена -->
    <el-dialog
      v-model="showCreateDialog"
      title="Создать обмен"
      width="600px"
      :close-on-click-modal="false"
    >
      <el-form
        ref="createFormRef"
        :model="createForm"
        :rules="createRules"
        label-width="150px"
      >
        <el-form-item label="Тип операции" prop="type">
          <el-radio-group v-model="createForm.type" @change="calculateUzs">
            <el-radio-button value="buy">
              <el-icon><ShoppingCart /></el-icon>
              Покупка USD
            </el-radio-button>
            <el-radio-button value="sell">
              <el-icon><Sell /></el-icon>
              Продажа USD
            </el-radio-button>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="Сумма USD" prop="usd_amount">
          <el-input-number
            v-model="createForm.usd_amount"
            :min="0"
            :precision="2"
            :step="10"
            @change="calculateUzs"
            style="width: 100%"
          >
            <template #prefix>$</template>
          </el-input-number>
        </el-form-item>

        <el-divider />

        <el-form-item label="Курс">
          <el-input-number
            v-model="appliedRate"
            disabled
            :precision="2"
            style="width: 100%"
          />
          <el-text type="info" size="small">
            {{ createForm.type === 'buy' ? 'Курс покупки' : 'Курс продажи' }}
          </el-text>
        </el-form-item>

        <el-form-item label="Сумма UZS">
          <el-input-number
            v-model="calculatedUzs"
            disabled
            :precision="2"
            style="width: 100%"
          >
            <template #suffix>сўм</template>
          </el-input-number>
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="showCreateDialog = false">Отмена</el-button>
        <el-button type="primary" :loading="creating" @click="createExchange">
          Создать
        </el-button>
      </template>
    </el-dialog>

    <!-- Диалог просмотра обмена -->
    <el-dialog v-model="showViewDialog" title="Детали обмена" width="500px">
      <el-descriptions :column="1" border v-if="selectedExchange">
        <el-descriptions-item label="Дата">
          {{ selectedExchange.date }}
        </el-descriptions-item>
        <el-descriptions-item label="Время">
          {{ selectedExchange.time }}
        </el-descriptions-item>
        <el-descriptions-item label="Тип">
          <el-tag :type="selectedExchange.type === 'buy' ? 'success' : 'warning'">
            {{ selectedExchange.type === 'buy' ? 'Покупка USD' : 'Продажа USD' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="Сумма USD">
          <strong>${{ formatCurrency(selectedExchange.usd_amount) }}</strong>
        </el-descriptions-item>
        <el-descriptions-item label="Курс">
          {{ formatCurrency(selectedExchange.rate) }} UZS
        </el-descriptions-item>
        <el-descriptions-item label="Сумма UZS">
          <strong>{{ formatCurrency(selectedExchange.uzs_amount) }} сўм</strong>
        </el-descriptions-item>
        <el-descriptions-item label="Кассир">
          {{ selectedExchange.cashier?.full_name }}
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
import { Plus, Refresh, View, ShoppingCart, Sell } from '@element-plus/icons-vue';
import axios from 'axios';

const loading = ref(false);
const creating = ref(false);
const exchanges = ref([]);
const currentRate = ref(null);
const currentPage = ref(1);
const pageSize = ref(20);
const total = ref(0);

const showCreateDialog = ref(false);
const showViewDialog = ref(false);
const selectedExchange = ref(null);
const createFormRef = ref(null);

const filters = reactive({
  date: null,
  type: null,
});

const createForm = reactive({
  type: 'buy',
  usd_amount: 0,
});

const calculatedUzs = ref(0);

const appliedRate = computed(() => {
  if (!currentRate.value) return 0;
  return createForm.type === 'buy'
    ? parseFloat(currentRate.value.buy_rate)
    : parseFloat(currentRate.value.sell_rate);
});

const createRules = {
  type: [{ required: true, message: 'Выберите тип операции', trigger: 'change' }],
  usd_amount: [
    { required: true, message: 'Введите сумму', trigger: 'change' },
    { type: 'number', min: 0.01, message: 'Сумма должна быть больше 0', trigger: 'change' },
  ],
};

const loadExchanges = async () => {
  loading.value = true;
  try {
    const params = {
      page: currentPage.value,
      per_page: pageSize.value,
      ...filters,
    };

    const response = await axios.get('/api/exchanges', { params });

    if (response.data.success) {
      exchanges.value = response.data.data;
      total.value = response.data.meta.total;
    }
  } catch (error) {
    ElMessage.error('Ошибка при загрузке обменов: ' + error.message);
  } finally {
    loading.value = false;
  }
};

const loadCurrentRate = async () => {
  try {
    const response = await axios.get('/api/rates');
    if (response.data.success && response.data.data.length > 0) {
      currentRate.value = response.data.data.find(r => r.is_active) || response.data.data[0];
    }
  } catch (error) {
    ElMessage.error('Ошибка при загрузке курса');
  }
};

const calculateUzs = () => {
  if (!createForm.usd_amount || !currentRate.value) {
    calculatedUzs.value = 0;
    return;
  }

  const rate = createForm.type === 'buy'
    ? parseFloat(currentRate.value.buy_rate)
    : parseFloat(currentRate.value.sell_rate);

  calculatedUzs.value = createForm.usd_amount * rate;
};

const createExchange = async () => {
  if (!createFormRef.value) return;

  await createFormRef.value.validate(async (valid) => {
    if (!valid) return;

    if (!currentRate.value) {
      ElMessage.error('Курс не установлен. Невозможно создать обмен.');
      return;
    }

    creating.value = true;
    try {
      const response = await axios.post('/api/exchanges', createForm);

      if (response.data.success) {
        ElMessage.success(response.data.message || 'Обмен успешно создан');
        showCreateDialog.value = false;
        resetCreateForm();
        await loadExchanges();
      }
    } catch (error) {
      ElMessage.error(error.response?.data?.message || 'Ошибка при создании обмена');
    } finally {
      creating.value = false;
    }
  });
};

const viewExchange = (exchange) => {
  selectedExchange.value = exchange;
  showViewDialog.value = true;
};

const resetFilters = () => {
  filters.date = null;
  filters.type = null;
  loadExchanges();
};

const resetCreateForm = () => {
  Object.assign(createForm, {
    type: 'buy',
    usd_amount: 0,
  });
  calculatedUzs.value = 0;
  createFormRef.value?.resetFields();
};

const formatCurrency = (value) => {
  if (!value) return '0.00';
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value);
};

onMounted(() => {
  loadExchanges();
  loadCurrentRate();
});
</script>

<style scoped>
.exchanges-page {
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

.rate-card {
  margin-bottom: 0;
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
