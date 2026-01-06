<template>
  <div class="reports-page">
    <el-card class="header-card">
      <h2>Мои отчеты</h2>
    </el-card>

    <el-card class="filters-card">
      <el-form :inline="true" :model="filters">
        <el-form-item label="Период">
          <el-date-picker
            v-model="dateRange"
            type="daterange"
            range-separator="по"
            start-placeholder="Начало"
            end-placeholder="Конец"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            @change="onDateRangeChange"
          />
        </el-form-item>
        <el-form-item label="Тип отчета">
          <el-select v-model="reportType" placeholder="Выберите тип" @change="generateReport">
            <el-option label="Платежи" value="payments" />
            <el-option label="Обмен валют" value="exchanges" />
            <el-option label="Кредиты" value="credits" />
            <el-option label="Инкассация" value="incashes" />
            <el-option label="Общий отчет" value="general" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Document" @click="generateReport" :loading="loading">
            Сформировать
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- Общая статистика -->
    <el-row :gutter="20" v-if="reportData && reportType === 'general'">
      <el-col :span="6">
        <el-card class="stat-card">
          <el-statistic title="Платежей" :value="reportData.payments?.count || 0">
            <template #suffix>
              <el-icon><Coin /></el-icon>
            </template>
          </el-statistic>
          <div class="stat-footer">
            {{ formatCurrency(reportData.payments?.total || 0) }} сўм
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="stat-card">
          <el-statistic title="Обменов" :value="reportData.exchanges?.count || 0">
            <template #suffix>
              <el-icon><Money /></el-icon>
            </template>
          </el-statistic>
          <div class="stat-footer">
            {{ formatCurrency(reportData.exchanges?.total || 0) }} сўм
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="stat-card">
          <el-statistic title="Кредитов" :value="reportData.credits?.count || 0">
            <template #suffix>
              <el-icon><Wallet /></el-icon>
            </template>
          </el-statistic>
          <div class="stat-footer">
            {{ formatCurrency(reportData.credits?.total || 0) }} сўм
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="stat-card">
          <el-statistic title="Инкассаций" :value="reportData.incashes?.count || 0">
            <template #suffix>
              <el-icon><Suitcase /></el-icon>
            </template>
          </el-statistic>
          <div class="stat-footer">
            {{ formatCurrency(reportData.incashes?.total || 0) }} сўм
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- Таблица платежей -->
    <el-card class="table-card" v-if="reportData && reportType === 'payments'">
      <h3>Платежи за выбранный период</h3>
      <el-table :data="reportData" stripe style="width: 100%">
        <el-table-column prop="date" label="Дата" width="120" />
        <el-table-column label="Тип платежа" min-width="200">
          <template #default="scope">
            {{ scope.row.payment_type?.name }}
          </template>
        </el-table-column>
        <el-table-column prop="payer_name" label="Плательщик" min-width="150" />
        <el-table-column label="Сумма" width="130" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.amount) }}
          </template>
        </el-table-column>
        <el-table-column label="Комиссия" width="120" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.commission) }}
          </template>
        </el-table-column>
        <el-table-column label="Итого" width="130" align="right">
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
      </el-table>
      <div class="report-summary">
        <strong>Всего платежей:</strong> {{ reportData.length }} <br />
        <strong>Общая сумма:</strong> {{ formatCurrency(calculateSum(reportData, 'total')) }} сўм
      </div>
    </el-card>

    <!-- Таблица обменов -->
    <el-card class="table-card" v-if="reportData && reportType === 'exchanges'">
      <h3>Обмен валют за выбранный период</h3>
      <el-table :data="reportData" stripe style="width: 100%">
        <el-table-column prop="date" label="Дата" width="120" />
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
            <strong>{{ formatCurrency(scope.row.uzs_amount) }}</strong>
          </template>
        </el-table-column>
      </el-table>
      <div class="report-summary">
        <strong>Всего обменов:</strong> {{ reportData.length }} <br />
        <strong>Общая сумма USD:</strong> ${{ formatCurrency(calculateSum(reportData, 'usd_amount')) }} <br />
        <strong>Общая сумма UZS:</strong> {{ formatCurrency(calculateSum(reportData, 'uzs_amount')) }} сўм
      </div>
    </el-card>

    <!-- Таблица кредитов -->
    <el-card class="table-card" v-if="reportData && reportType === 'credits'">
      <h3>Кредиты за выбранный период</h3>
      <el-table :data="reportData" stripe style="width: 100%">
        <el-table-column prop="date" label="Дата" width="120" />
        <el-table-column prop="recipient" label="Получатель" min-width="180" />
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
        <el-table-column label="Статус" width="130">
          <template #default="scope">
            <el-tag :type="getStatusType(scope.row.status)">
              {{ getStatusLabel(scope.row.status) }}
            </el-tag>
          </template>
        </el-table-column>
      </el-table>
      <div class="report-summary">
        <strong>Всего кредитов:</strong> {{ reportData.length }} <br />
        <strong>Общая сумма:</strong> {{ formatCurrency(calculateSum(reportData, 'credit')) }} сўм
      </div>
    </el-card>

    <!-- Таблица инкассаций -->
    <el-card class="table-card" v-if="reportData && reportType === 'incashes'">
      <h3>Инкассация за выбранный период</h3>
      <el-table :data="reportData" stripe style="width: 100%">
        <el-table-column prop="date" label="Дата" width="120" />
        <el-table-column label="Наличные" width="160" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.cash_uzs) }}
          </template>
        </el-table-column>
        <el-table-column label="Безнал" width="160" align="right">
          <template #default="scope">
            {{ formatCurrency(scope.row.cashless_uzs) }}
          </template>
        </el-table-column>
        <el-table-column label="USD" width="130" align="right">
          <template #default="scope">
            ${{ formatCurrency(scope.row.usd) }}
          </template>
        </el-table-column>
        <el-table-column label="Итого" min-width="150" align="right">
          <template #default="scope">
            <strong>{{ formatCurrency(scope.row.total) }}</strong>
          </template>
        </el-table-column>
      </el-table>
      <div class="report-summary">
        <strong>Всего инкассаций:</strong> {{ reportData.length }} <br />
        <strong>Наличные:</strong> {{ formatCurrency(calculateSum(reportData, 'cash_uzs')) }} сўм<br />
        <strong>Безналичные:</strong> {{ formatCurrency(calculateSum(reportData, 'cashless_uzs')) }} сўм<br />
        <strong>USD:</strong> ${{ formatCurrency(calculateSum(reportData, 'usd')) }}<br />
        <strong>Общая сумма:</strong> {{ formatCurrency(calculateSum(reportData, 'total')) }} сўм
      </div>
    </el-card>

    <el-empty v-if="!loading && !reportData" description="Выберите период и тип отчета" />
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { ElMessage } from 'element-plus';
import { Document, Coin, Money, Wallet, Suitcase } from '@element-plus/icons-vue';
import api from '@/services/api';

const loading = ref(false);
const dateRange = ref([]);
const reportType = ref('general');
const reportData = ref(null);

const filters = reactive({
  start_date: null,
  end_date: null,
});

const onDateRangeChange = (dates) => {
  if (dates && dates.length === 2) {
    filters.start_date = dates[0];
    filters.end_date = dates[1];
  } else {
    filters.start_date = null;
    filters.end_date = null;
  }
};

const generateReport = async () => {
  if (!filters.start_date || !filters.end_date) {
    ElMessage.warning('Выберите период для отчета');
    return;
  }

  if (!reportType.value) {
    ElMessage.warning('Выберите тип отчета');
    return;
  }

  loading.value = true;
  reportData.value = null;

  try {
    let endpoint = '';
    const params = {
      start_date: filters.start_date,
      end_date: filters.end_date,
    };

    if (reportType.value === 'general') {
      endpoint = '/api/reports/general';
    } else if (reportType.value === 'payments') {
      endpoint = '/api/payments';
    } else if (reportType.value === 'exchanges') {
      endpoint = '/api/exchanges';
    } else if (reportType.value === 'credits') {
      endpoint = '/api/credits';
    } else if (reportType.value === 'incashes') {
      endpoint = '/api/incashes';
    }

    const response = await axios.get(endpoint, { params });

    if (response.data.success) {
      if (reportType.value === 'general') {
        reportData.value = response.data.data;
      } else {
        reportData.value = response.data.data;
      }
      ElMessage.success('Отчет успешно сформирован');
    }
  } catch (error) {
    ElMessage.error('Ошибка при формировании отчета: ' + (error.response?.data?.message || error.message));
  } finally {
    loading.value = false;
  }
};

const calculateSum = (data, field) => {
  if (!data || !Array.isArray(data)) return 0;
  return data.reduce((sum, item) => sum + (parseFloat(item[field]) || 0), 0);
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
</script>

<style scoped>
.reports-page {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.header-card h2 {
  margin: 0;
  font-size: 24px;
  color: #303133;
}

.filters-card {
  margin-bottom: 0;
}

.stat-card {
  text-align: center;
}

.stat-footer {
  margin-top: 12px;
  color: #909399;
  font-size: 14px;
}

.table-card {
  margin-bottom: 0;
}

.table-card h3 {
  margin: 0 0 20px 0;
  color: #303133;
}

.report-summary {
  margin-top: 20px;
  padding: 15px;
  background-color: #f5f7fa;
  border-radius: 4px;
  line-height: 1.8;
}
</style>
