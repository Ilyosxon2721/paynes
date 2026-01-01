import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/services/api';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('token') || null);
  const loading = ref(false);
  const error = ref(null);

  const isAuthenticated = computed(() => !!token.value);
  const isAdmin = computed(() => user.value?.position === 'admin');

  async function login(credentials) {
    loading.value = true;
    error.value = null;

    try {
      const response = await api.post('/login', credentials);

      if (response.data.success) {
        token.value = response.data.data.token;
        user.value = response.data.data.user;
        localStorage.setItem('token', token.value);
        return true;
      }

      return false;
    } catch (err) {
      error.value = err.response?.data?.message || 'Ошибка авторизации';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    loading.value = true;

    try {
      await api.post('/logout');
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      user.value = null;
      token.value = null;
      localStorage.removeItem('token');
      loading.value = false;
    }
  }

  async function checkAuth() {
    if (!token.value) return;

    try {
      const response = await api.get('/user');

      if (response.data.success) {
        user.value = response.data.data;
      } else {
        await logout();
      }
    } catch (err) {
      await logout();
    }
  }

  function hasPermission(permission) {
    return user.value?.permissions?.includes(permission) || false;
  }

  function hasRole(role) {
    return user.value?.roles?.includes(role) || false;
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    isAdmin,
    login,
    logout,
    checkAuth,
    hasPermission,
    hasRole
  };
});
