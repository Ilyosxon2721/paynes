import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Auth/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/cashier',
    component: () => import('@/layouts/CashierLayout.vue'),
    meta: { requiresAuth: true, role: 'cashier' },
    children: [
      {
        path: 'payments',
        name: 'CashierPayments',
        component: () => import('@/views/cashier/Payments.vue')
      },
      {
        path: 'exchanges',
        name: 'CashierExchanges',
        component: () => import('@/views/cashier/Exchanges.vue')
      },
      {
        path: 'credits',
        name: 'CashierCredits',
        component: () => import('@/views/cashier/Credits.vue')
      },
      {
        path: 'incashes',
        name: 'CashierIncashes',
        component: () => import('@/views/cashier/Incashes.vue')
      },
      {
        path: 'reports',
        name: 'CashierReports',
        component: () => import('@/views/cashier/Reports.vue')
      },
      {
        path: 'tickets',
        name: 'CashierTickets',
        component: () => import('@/views/Tickets/Index.vue')
      }
    ]
  },
  {
    path: '/',
    component: () => import('@/layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue')
      },
      {
        path: 'payments',
        name: 'Payments',
        component: () => import('@/views/Payments/Index.vue')
      },
      {
        path: 'payments/create',
        name: 'PaymentsCreate',
        component: () => import('@/views/Payments/Create.vue')
      },
      {
        path: 'payment-types',
        name: 'PaymentTypes',
        component: () => import('@/views/PaymentTypes/Index.vue')
      },
      {
        path: 'exchanges',
        name: 'Exchanges',
        component: () => import('@/views/Exchanges/Index.vue')
      },
      {
        path: 'exchanges/create',
        name: 'ExchangesCreate',
        component: () => import('@/views/Exchanges/Create.vue')
      },
      {
        path: 'credits',
        name: 'Credits',
        component: () => import('@/views/Credits/Index.vue')
      },
      {
        path: 'credits/create',
        name: 'CreditsCreate',
        component: () => import('@/views/Credits/Create.vue')
      },
      {
        path: 'incashes',
        name: 'Incashes',
        component: () => import('@/views/Incashes/Index.vue')
      },
      {
        path: 'rates',
        name: 'Rates',
        component: () => import('@/views/Rates/Index.vue')
      },
      {
        path: 'tickets',
        name: 'Tickets',
        component: () => import('@/views/Tickets/Index.vue')
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Проверка аутентификации пользователя
  if (authStore.isAuthenticated && !authStore.user) {
    await authStore.checkAuth();
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    // Переадресация в зависимости от роли после входа
    if (authStore.user?.position === 'cashier') {
      next({ name: 'CashierPayments' });
    } else {
      next({ name: 'Dashboard' });
    }
  } else {
    next();
  }
});

export default router;
