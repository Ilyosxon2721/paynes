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
        path: 'incashes/create',
        name: 'IncashesCreate',
        component: () => import('@/views/Incashes/Create.vue')
      },
      {
        path: 'rates',
        name: 'Rates',
        component: () => import('@/views/Rates/Index.vue')
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'Dashboard' });
  } else {
    next();
  }
});

export default router;
