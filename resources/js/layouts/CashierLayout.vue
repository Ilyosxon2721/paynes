<template>
  <el-container class="cashier-layout">
    <el-aside width="240px" class="sidebar">
      <div class="logo">
        <svg class="logo-icon" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <path d="M 50 150 L 50 50 Q 50 30 70 30 L 120 30 Q 150 30 150 60 Q 150 90 120 90 L 80 90 L 120 130 L 80 130 Z"
                fill="white" stroke="none"/>
        </svg>
        <h2>Paynes</h2>
        <p class="tagline">Кассир</p>
        <el-tag type="info" size="small">{{ user?.branch }}</el-tag>
      </div>

                  <el-menu
        :default-active="activeMenu"
        router
        background-color="#0D4F8F"
        text-color="#bfcbd9"
        active-text-color="#ffffff"
      >
        <el-menu-item index="/cashier/payments">
          <el-icon><Coin /></el-icon>
          <span>Платежи</span>
        </el-menu-item>

        <el-menu-item index="/cashier/exchanges">
          <el-icon><Money /></el-icon>
          <span>Обмен валют</span>
        </el-menu-item>

        <el-menu-item index="/cashier/credits">
          <el-icon><Wallet /></el-icon>
          <span>Кредиты</span>
        </el-menu-item>

        <el-menu-item index="/cashier/incashes">
          <el-icon><Suitcase /></el-icon>
          <span>Инкассация</span>
        </el-menu-item>

        <el-menu-item index="/cashier/reports">
          <el-icon><Document /></el-icon>
          <span>Отчеты</span>
        </el-menu-item>
        <el-menu-item index="/cashier/tickets">
          <el-icon><ChatLineSquare /></el-icon>
          <span>Чат/Тикеты</span>
        </el-menu-item>
      </el-menu>

    </el-aside>

    <el-container>
      <el-header class="header">
        <div class="header-content">
          <div class="user-info">
            <el-icon><User /></el-icon>
            <span class="username">{{ user?.full_name }}</span>
            <el-tag type="success" size="small">{{ user?.position }}</el-tag>
          </div>

          <div class="header-actions">
            <div class="shift-controls-wrapper">
              <ShiftControls
                @shift-opened="handleShiftOpened"
                @shift-closed="handleShiftClosed"
                @shift-changed="handleShiftChanged"
              />
            </div>
            <el-button @click="logout" type="danger" :icon="SwitchButton">Выйти</el-button>
          </div>
        </div>
      </el-header>

      <el-main class="main-content">
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import ShiftControls from '../components/ShiftControls.vue';
import { ElMessage } from 'element-plus';
import {
  Coin,
  Money,
  Wallet,
  Suitcase,
  Document,
  ChatLineSquare,
  User,
  SwitchButton
} from '@element-plus/icons-vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const user = computed(() => authStore.user);
const activeMenu = computed(() => route.path);
const currentShift = ref(null);

const logout = async () => {
  await authStore.logout();
  router.push('/login');
};

const handleShiftOpened = (shift) => {
  currentShift.value = shift;
  ElMessage.success('Смена успешно открыта');
};

const handleShiftClosed = (shift) => {
  currentShift.value = null;
  ElMessage.success('Смена успешно закрыта');
};

const handleShiftChanged = (shift) => {
  currentShift.value = shift;
};

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
  }
});
</script>

<style scoped>
.cashier-layout {
  height: 100vh;
  overflow: hidden;
}

.sidebar {
  background: var(--paynes-gradient-primary);
  box-shadow: var(--paynes-shadow-lg);
}

.logo {
  padding: 24px 20px;
  text-align: center;
  border-bottom: 2px solid rgba(255,255,255,.2);
  margin-bottom: 10px;
}

.logo .logo-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 12px;
  display: block;
}

.logo h2 {
  color: #fff;
  margin: 0 0 4px 0;
  font-size: 26px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.logo .tagline {
  color: rgba(255, 255, 255, 0.85);
  font-size: 13px;
  margin: 0 0 12px 0;
  font-weight: 400;
}

.el-menu {
  border-right: none;
}

.header {
  background-color: #fff;
  box-shadow: 0 1px 4px rgba(0,21,41,.08);
  padding: 0 20px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.shift-controls-wrapper {
  margin-right: 8px;
}

.shift-controls-wrapper :deep(.shift-controls) {
  margin: 0;
}

.username {
  font-weight: 500;
  color: #303133;
}

.main-content {
  background-color: var(--paynes-gray-50);
  padding: 20px;
  overflow-y: auto;
}

/* Override Element Plus menu active styles */
:deep(.el-menu-item.is-active) {
  background-color: rgba(255, 255, 255, 0.15) !important;
  border-left: 4px solid white;
  font-weight: 600;
}
</style>



