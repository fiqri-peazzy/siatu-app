const CACHE_NAME = 'situ-sma-cache-v1';
const urlsToCache = [
    '/',
    '/manifest.json',
    '/images/logo-removebg-preview.png'
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', function(event) {
    // Basic network-first strategy for a dynamic app like Livewire
    event.respondWith(
        fetch(event.request).catch(function() {
            return caches.match(event.request);
        })
    );
});
