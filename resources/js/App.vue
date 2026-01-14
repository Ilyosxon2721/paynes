<template>
  <div id="app">
    <!-- PWA Status (Offline/Update banners) -->
    <PWAStatus />

    <!-- Main App Content -->
    <router-view />

    <!-- PWA Install Prompt -->
    <PWAInstallPrompt />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import PWAInstallPrompt from '@/components/PWAInstallPrompt.vue';
import PWAStatus from '@/components/PWAStatus.vue';

const authStore = useAuthStore();

onMounted(() => {
  // Check if user is already logged in
  authStore.checkAuth();
});
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  background-color: #f5f5f5;
}

#app {
  min-height: 100vh;
}
</style>
