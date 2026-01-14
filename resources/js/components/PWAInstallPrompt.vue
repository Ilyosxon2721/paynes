<template>
  <Transition name="slide-up">
    <div v-if="showPrompt" class="pwa-install-prompt">
      <div class="pwa-install-content">
        <div class="pwa-icon">
          <img src="/icons/icon.svg" alt="Paynes" />
        </div>
        <div class="pwa-info">
          <h4>Установить Paynes</h4>
          <p>Добавьте приложение на главный экран для быстрого доступа</p>
        </div>
        <div class="pwa-actions">
          <button class="btn-install" @click="installPWA">
            Установить
          </button>
          <button class="btn-close" @click="dismissPrompt">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const showPrompt = ref(false);
const deferredPrompt = ref(null);

const handleBeforeInstall = (e) => {
  e.preventDefault();
  deferredPrompt.value = e;

  // Показываем через небольшую задержку
  setTimeout(() => {
    // Проверяем, не закрывал ли пользователь ранее
    const dismissed = localStorage.getItem('pwa-prompt-dismissed');
    if (!dismissed) {
      showPrompt.value = true;
    }
  }, 3000);
};

const installPWA = async () => {
  if (!deferredPrompt.value) return;

  deferredPrompt.value.prompt();

  const { outcome } = await deferredPrompt.value.userChoice;

  if (outcome === 'accepted') {
    console.log('PWA installed');
  }

  deferredPrompt.value = null;
  showPrompt.value = false;
};

const dismissPrompt = () => {
  showPrompt.value = false;
  // Запоминаем на 7 дней
  localStorage.setItem('pwa-prompt-dismissed', Date.now().toString());
};

onMounted(() => {
  window.addEventListener('beforeinstallprompt', handleBeforeInstall);

  window.addEventListener('appinstalled', () => {
    showPrompt.value = false;
    deferredPrompt.value = null;
  });
});

onUnmounted(() => {
  window.removeEventListener('beforeinstallprompt', handleBeforeInstall);
});
</script>

<style scoped>
.pwa-install-prompt {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 9999;
  width: calc(100% - 40px);
  max-width: 400px;
}

.pwa-install-content {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15),
              0 0 0 1px rgba(0, 0, 0, 0.05);
}

.pwa-icon {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  border-radius: 12px;
  overflow: hidden;
}

.pwa-icon img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pwa-info {
  flex: 1;
  min-width: 0;
}

.pwa-info h4 {
  margin: 0 0 4px;
  font-size: 15px;
  font-weight: 600;
  color: #1f2937;
}

.pwa-info p {
  margin: 0;
  font-size: 13px;
  color: #6b7280;
  line-height: 1.3;
}

.pwa-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-install {
  padding: 10px 16px;
  background: linear-gradient(135deg, #1A77C9 0%, #0D4F8F 100%);
  color: white;
  font-size: 14px;
  font-weight: 600;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-install:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(26, 119, 201, 0.4);
}

.btn-install:active {
  transform: translateY(0);
}

.btn-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  padding: 0;
  background: #f3f4f6;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s ease;
}

.btn-close:hover {
  background: #e5e7eb;
  color: #374151;
}

/* Animations */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateX(-50%) translateY(100%);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(100%);
}

/* Mobile responsive */
@media (max-width: 480px) {
  .pwa-install-prompt {
    bottom: 16px;
    width: calc(100% - 32px);
  }

  .pwa-install-content {
    padding: 12px;
  }

  .pwa-icon {
    width: 40px;
    height: 40px;
  }

  .pwa-info h4 {
    font-size: 14px;
  }

  .pwa-info p {
    font-size: 12px;
  }

  .btn-install {
    padding: 8px 12px;
    font-size: 13px;
  }
}
</style>
