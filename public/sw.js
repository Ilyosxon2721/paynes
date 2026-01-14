/**
 * Paynes PWA Service Worker
 * Кеширование и офлайн-режим
 */

const CACHE_NAME = 'paynes-v1';
const STATIC_CACHE = 'paynes-static-v1';
const DYNAMIC_CACHE = 'paynes-dynamic-v1';
const API_CACHE = 'paynes-api-v1';

// Статические ресурсы для кеширования при установке
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/favicon.svg',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/offline.html',
];

// Паттерны для кеширования
const CACHE_STRATEGIES = {
  // Статические ресурсы - Cache First
  static: [
    /\.(?:js|css|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|webp|ico)$/,
    /fonts\.googleapis\.com/,
    /fonts\.gstatic\.com/,
  ],
  // API запросы - Network First с fallback на кеш
  api: [
    /\/api\//,
  ],
  // HTML страницы - Network First
  pages: [
    /\/$/,
    /\/cashier/,
    /\/merchant/,
    /\/admin/,
  ],
};

// Установка Service Worker
self.addEventListener('install', (event) => {
  console.log('[SW] Installing Service Worker...');

  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => {
        console.log('[SW] Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => self.skipWaiting())
      .catch((error) => {
        console.error('[SW] Failed to cache static assets:', error);
      })
  );
});

// Активация - очистка старых кешей
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating Service Worker...');

  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames
            .filter((cacheName) => {
              return cacheName.startsWith('paynes-') &&
                     cacheName !== STATIC_CACHE &&
                     cacheName !== DYNAMIC_CACHE &&
                     cacheName !== API_CACHE;
            })
            .map((cacheName) => {
              console.log('[SW] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            })
        );
      })
      .then(() => self.clients.claim())
  );
});

// Обработка fetch запросов
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Пропускаем не-GET запросы
  if (request.method !== 'GET') {
    return;
  }

  // Пропускаем запросы к другим доменам (кроме CDN)
  if (url.origin !== location.origin &&
      !url.hostname.includes('fonts.googleapis.com') &&
      !url.hostname.includes('fonts.gstatic.com')) {
    return;
  }

  // Определяем стратегию кеширования
  if (isApiRequest(url)) {
    event.respondWith(networkFirstStrategy(request, API_CACHE));
  } else if (isStaticAsset(url)) {
    event.respondWith(cacheFirstStrategy(request, STATIC_CACHE));
  } else {
    event.respondWith(networkFirstStrategy(request, DYNAMIC_CACHE));
  }
});

// Cache First - для статических ресурсов
async function cacheFirstStrategy(request, cacheName) {
  const cachedResponse = await caches.match(request);

  if (cachedResponse) {
    // Обновляем кеш в фоне
    updateCache(request, cacheName);
    return cachedResponse;
  }

  return fetchAndCache(request, cacheName);
}

// Network First - для API и страниц
async function networkFirstStrategy(request, cacheName) {
  try {
    const networkResponse = await fetch(request);

    // Кешируем успешные ответы
    if (networkResponse.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, networkResponse.clone());
    }

    return networkResponse;
  } catch (error) {
    console.log('[SW] Network failed, trying cache:', request.url);

    const cachedResponse = await caches.match(request);

    if (cachedResponse) {
      return cachedResponse;
    }

    // Для навигации показываем офлайн-страницу
    if (request.mode === 'navigate') {
      return caches.match('/offline.html');
    }

    throw error;
  }
}

// Вспомогательные функции
async function fetchAndCache(request, cacheName) {
  try {
    const response = await fetch(request);

    if (response.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, response.clone());
    }

    return response;
  } catch (error) {
    // Возвращаем офлайн-страницу для навигации
    if (request.mode === 'navigate') {
      return caches.match('/offline.html');
    }
    throw error;
  }
}

async function updateCache(request, cacheName) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, response);
    }
  } catch (error) {
    // Игнорируем ошибки фонового обновления
  }
}

function isStaticAsset(url) {
  return CACHE_STRATEGIES.static.some(pattern => pattern.test(url.href));
}

function isApiRequest(url) {
  return CACHE_STRATEGIES.api.some(pattern => pattern.test(url.pathname));
}

// Обработка push-уведомлений
self.addEventListener('push', (event) => {
  if (!event.data) return;

  const data = event.data.json();

  const options = {
    body: data.body || 'Новое уведомление от Paynes',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/badge-72x72.png',
    vibrate: [100, 50, 100],
    data: {
      url: data.url || '/',
    },
    actions: data.actions || [
      { action: 'open', title: 'Открыть' },
      { action: 'close', title: 'Закрыть' },
    ],
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'Paynes', options)
  );
});

// Обработка клика по уведомлению
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'close') return;

  const url = event.notification.data?.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true })
      .then((clientList) => {
        // Ищем открытое окно
        for (const client of clientList) {
          if (client.url === url && 'focus' in client) {
            return client.focus();
          }
        }
        // Открываем новое окно
        if (clients.openWindow) {
          return clients.openWindow(url);
        }
      })
  );
});

// Фоновая синхронизация
self.addEventListener('sync', (event) => {
  console.log('[SW] Background sync:', event.tag);

  if (event.tag === 'sync-payments') {
    event.waitUntil(syncPendingPayments());
  }

  if (event.tag === 'sync-shifts') {
    event.waitUntil(syncShiftData());
  }
});

async function syncPendingPayments() {
  // Логика синхронизации платежей при восстановлении сети
  console.log('[SW] Syncing pending payments...');
}

async function syncShiftData() {
  // Логика синхронизации данных смены
  console.log('[SW] Syncing shift data...');
}

console.log('[SW] Service Worker loaded');
