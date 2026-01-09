<template>
  <div class="dashboard">
    <h1 class="page-title">Главная панель</h1>

    <div class="welcome-card">
      <h2>Добро пожаловать, {{ authStore.user?.full_name }}!</h2>
      <p>{{ positionText }} - {{ authStore.user?.branch || 'Главный офис' }}</p>
      <div v-if="isCashier && cashierData?.shift" class="shift-info">
        <span class="shift-badge">Смена открыта: {{ formatShiftTime(cashierData.shift.opened_at) }}</span>
      </div>
    </div>

    <div v-if="isAdmin" class="branch-filter">
      <div class="filter-group">
        <label>Филиал</label>
        <select v-model="selectedBranch" @change="loadStats" class="filter-select">
          <option value="">Все филиалы</option>
          <option v-for="branch in branches" :key="branch" :value="branch">
            {{ branch }}
          </option>
        </select>
      </div>
    </div>

    <h3 v-if="isAdmin" class="section-title">{{ branchTitle }}</h3>

    <!-- Balance Cards -->
    <BalanceCards v-if="isCashier || isAdmin" :balances="balances" />

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">💳</div>
        <div class="stat-info">
          <div class="stat-label">Платежи</div>
          <div class="stat-value" v-if="loading">...</div>
          <div class="stat-value" v-else>
            <div class="stat-count">{{ stats.payments?.count || 0 }}</div>
            <div class="stat-amount">{{ formatNumber(stats.payments?.total) }} UZS</div>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">💱</div>
        <div class="stat-info">
          <div class="stat-label">Обмен валют</div>
          <div class="stat-value" v-if="loading">...</div>
          <div class="stat-value" v-else>
            <div class="stat-count">{{ stats.exchanges?.count || 0 }}</div>
            <div class="stat-amount">{{ formatNumber(stats.exchanges?.total) }} UZS</div>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
          <div class="stat-label">Кредиты</div>
          <div class="stat-value" v-if="loading">...</div>
          <div class="stat-value" v-else>
            <div class="stat-count">{{ stats.credits?.count || 0 }}</div>
            <div class="stat-amount">{{ formatNumber(stats.credits?.total) }} UZS</div>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">💵</div>
        <div class="stat-info">
          <div class="stat-label">Инкассация</div>
          <div class="stat-value" v-if="loading">...</div>
          <div class="stat-value" v-else>
            <div class="stat-count">{{ stats.incashes?.count || 0 }}</div>
            <div class="stat-amount">{{ formatNumber(stats.incashes?.total) }} UZS</div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isAdmin && selectedBranch" class="overall-section">
      <h3 class="section-title">Общий итог</h3>
      <BalanceCards :balances="overallBalances" />
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">💳</div>
          <div class="stat-info">
            <div class="stat-label">Платежи</div>
            <div class="stat-value" v-if="loading">...</div>
            <div class="stat-value" v-else>
              <div class="stat-count">{{ overallStats.payments?.count || 0 }}</div>
              <div class="stat-amount">{{ formatNumber(overallStats.payments?.total) }} UZS</div>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">💱</div>
          <div class="stat-info">
            <div class="stat-label">Обмен валют</div>
            <div class="stat-value" v-if="loading">...</div>
            <div class="stat-value" v-else>
              <div class="stat-count">{{ overallStats.exchanges?.count || 0 }}</div>
              <div class="stat-amount">{{ formatNumber(overallStats.exchanges?.total) }} UZS</div>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">💰</div>
          <div class="stat-info">
            <div class="stat-label">Кредиты</div>
            <div class="stat-value" v-if="loading">...</div>
            <div class="stat-value" v-else>
              <div class="stat-count">{{ overallStats.credits?.count || 0 }}</div>
              <div class="stat-amount">{{ formatNumber(overallStats.credits?.total) }} UZS</div>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">💵</div>
          <div class="stat-info">
            <div class="stat-label">Инкассации</div>
            <div class="stat-value" v-if="loading">...</div>
            <div class="stat-value" v-else>
              <div class="stat-count">{{ overallStats.incashes?.count || 0 }}</div>
              <div class="stat-amount">{{ formatNumber(overallStats.incashes?.total) }} UZS</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="quick-actions">
      <h3>Быстрые действия</h3>
      <div class="action-buttons">
        <router-link to="/payments/create" class="action-btn">
          <span>💳</span>
          <span>Новый платеж</span>
        </router-link>

        <router-link to="/exchanges/create" class="action-btn">
          <span>💱</span>
          <span>Обмен валюты</span>
        </router-link>

        <router-link to="/credits/create" class="action-btn">
          <span>💰</span>
          <span>Новый кредит</span>
        </router-link>

        <router-link to="/incashes/create" class="action-btn">
          <span>💵</span>
          <span>Инкассация</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/services/api';
import BalanceCards from '@/components/BalanceCards.vue';

const authStore = useAuthStore();

const positionText = computed(() => {
  return authStore.user?.position === 'admin' ? 'Администратор' : 'Кассир';
});

const isCashier = computed(() => {
  return authStore.user?.position === 'cashier';
});

const isAdmin = computed(() => {
  return authStore.user?.position === 'admin';
});

const defaultStats = () => ({
  payments: { count: 0, total: 0 },
  exchanges: { count: 0, total: 0 },
  credits: { count: 0, total: 0 },
  incashes: { count: 0, total: 0 }
});

const defaultBalances = () => ({
  cash_uzs: 0,
  cashless_uzs: 0,
  card_uzs: 0,
  p2p_uzs: 0,
  cash_usd: 0,
});

const stats = ref(defaultStats());
const overallStats = ref(defaultStats());
const cashierData = ref(null);
const balances = ref(defaultBalances());
const overallBalances = ref(defaultBalances());
const branches = ref([]);
const selectedBranch = ref('');

const branchTitle = computed(() => {
  return selectedBranch.value ? `Филиал: ${selectedBranch.value}` : 'Все филиалы';
});

const loading = ref(false);

async function loadStats() {
  loading.value = true;
  try {
    if (isCashier.value) {
      // For cashiers, load detailed dashboard
      const response = await api.get('/reports/cashier-dashboard');
      if (response.data.success) {
        cashierData.value = response.data.data;
        balances.value = response.data.data.balances;

        // Map cashier data to stats for backward compatibility
        stats.value = {
          payments: {
            count: response.data.data.payments?.count || 0,
            total: response.data.data.payments?.total || 0
          },
          exchanges: {
            count: response.data.data.exchanges?.count || 0,
            total: response.data.data.exchanges?.total_uzs || 0
          },
          credits: {
            count: response.data.data.credits?.count || 0,
            total: response.data.data.credits?.total_issued || 0
          },
          incashes: {
            count: (response.data.data.incashes?.income?.count || 0) + (response.data.data.incashes?.expense?.count || 0),
            total: response.data.data.incashes?.balance || 0
          }
        };
      }
    } else {
      await loadAdminStats();
    }
  } catch (error) {
    console.error('Error loading stats:', error);
  } finally {
    loading.value = false;
  }
}

async function loadAdminStats() {
  const today = new Date().toISOString().split('T')[0];
  const baseParams = { start_date: today, end_date: today };

  const overallResponse = await api.get('/reports/general', { params: baseParams });
  if (overallResponse.data.success) {
    overallStats.value = overallResponse.data.data;
    overallBalances.value = overallResponse.data.data.balances || defaultBalances();
    branches.value = overallResponse.data.data.branches || [];
  }

  if (selectedBranch.value) {
    const branchResponse = await api.get('/reports/general', { params: { ...baseParams, branch: selectedBranch.value } });
    if (branchResponse.data.success) {
      stats.value = branchResponse.data.data;
      balances.value = branchResponse.data.data.balances || defaultBalances();
      return;
    }
  }

  stats.value = overallStats.value;
  balances.value = overallBalances.value;
}
function formatNumber(value) {
  return new Intl.NumberFormat('ru-RU', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value || 0);
}

function formatShiftTime(datetime) {
  if (!datetime) return '';
  const date = new Date(datetime);
  return date.toLocaleString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

onMounted(() => {
  loadStats();
});
</script>

<style scoped>
.dashboard {
  max-width: 1400px;
  margin: 0 auto;
}

.page-title {
  font-size: 32px;
  font-weight: 600;
  color: #2c3e50;
  margin: 0 0 30px 0;
}

.welcome-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px;
  border-radius: 12px;
  margin-bottom: 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.welcome-card h2 {
  margin: 0 0 8px 0;
  font-size: 24px;
  font-weight: 600;
}

.welcome-card p {
  margin: 0;
  opacity: 0.9;
  font-size: 16px;
}

.shift-info {
  margin-top: 16px;
}

.shift-badge {
  display: inline-block;
  padding: 8px 16px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
  backdrop-filter: blur(10px);
}

.branch-filter {
  background: white;
  padding: 16px 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  margin-bottom: 20px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  max-width: 320px;
}

.filter-group label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.filter-select {
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  background: white;
}

.section-title {
  margin: 0 0 16px 0;
  font-size: 20px;
  font-weight: 600;
  color: #2c3e50;
}

.overall-section {
  margin-top: 24px;
}


.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  font-size: 48px;
}

.stat-info {
  flex: 1;
}

.stat-label {
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 28px;
  font-weight: 600;
  color: #2c3e50;
}

.stat-count {
  font-size: 28px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 4px;
}

.stat-amount {
  font-size: 14px;
  font-weight: 500;
  color: #667eea;
}

.quick-actions {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.quick-actions h3 {
  margin: 0 0 20px 0;
  font-size: 20px;
  font-weight: 600;
  color: #2c3e50;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 24px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 10px;
  text-decoration: none;
  color: #2c3e50;
  font-weight: 500;
  transition: all 0.3s;
}

.action-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.action-btn span:first-child {
  font-size: 36px;
}

.action-btn span:last-child {
  font-size: 15px;
}
</style>






