self.addEventListener('install', event => {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('push', event => {
  if (Notification.permission != 'granted') {
    return;
  }

  const payload = event.data.json();
  if (!payload) {
    return;
  }
  if (payload.url && new URL(payload.url).origin !== self.location.origin) {
    return;
  }

  const options = {
    body: payload.body || undefined,
    data: payload.url,
    tag: payload.tag || undefined,
    icon: '/icon-192x192.png',
  };
  if ('badge' in Notification.prototype) {
    options.badge = '/badge-36x36.png';
  }
  event.waitUntil(self.registration.showNotification(payload.title, options));
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  if (event.notification.data) {
    event.waitUntil(self.clients.openWindow(event.notification.data));
  }
});
