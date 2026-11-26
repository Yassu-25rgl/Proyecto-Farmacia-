const CACHE_NAME = 'farmacia-sjb-static-v1';
const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.json',
    '/favicon.ico',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/icon.svg',
    '/css/bootstrap.css',
    '/css/custom.css',
    '/js/bootstrap.bundle.min.js',
    '/js/app.js',
    '/js/chart.umd.min.js',
    '/js/pwa.js'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName.startsWith('farmacia-sjb-') && cacheName !== CACHE_NAME)
                    .map((cacheName) => caches.delete(cacheName))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const requestUrl = new URL(request.url);

    if (request.method !== 'GET' || requestUrl.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(async () => (
                await caches.match('/offline.html')
                || new Response('Sin conexión a internet.', {
                    status: 503,
                    headers: { 'Content-Type': 'text/plain; charset=UTF-8' }
                })
            ))
        );
        return;
    }

    const isStaticAsset = ['/css/', '/js/', '/icons/'].some((path) => requestUrl.pathname.startsWith(path))
        || ['/favicon.ico', '/manifest.json'].includes(requestUrl.pathname);

    if (!isStaticAsset) {
        return;
    }

    event.respondWith(
        caches.match(request).then(async (cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            const response = await fetch(request);
            if (response.ok) {
                const cache = await caches.open(CACHE_NAME);
                await cache.put(request, response.clone());
            }
            return response;
        })
    );
});
