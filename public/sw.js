// Versi baru service worker langsung aktif & mengambil alih semua tab/PWA yang terbuka
// (tanpa menunggu semua jendela ditutup). SW ini tidak meng-cache halaman/aset,
// jadi aplikasi selalu memuat versi terbaru dari server.
self.addEventListener('install', function () {
    self.skipWaiting();
});

self.addEventListener('activate', function (e) {
    e.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        e.waitUntil(
            self.registration.showNotification(msg.title, {
                body: msg.body,
                icon: msg.icon || '/images/logo-removebg-preview.png',
                data: msg.data
            })
        );
    }
});

self.addEventListener('notificationclick', function (e) {
    e.notification.close();
    
    // Redirect to notifikasi page if action is clicked or general notification is clicked
    var targetUrl = e.notification.data?.url || '/guru/notifikasi';
    
    e.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
