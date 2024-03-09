const CACHE_PREFIX = 'dynamic-cache-';
const CACHE_VERSION = 'v1';

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(`${CACHE_PREFIX}${CACHE_VERSION}`).then(cache => {
            return cache.addAll([
                // キャッシュしたいファイルのリスト
                
                './check_session.php',
                './index.js',
                './index.php',
                './manifest.json',
                './qrGenerate.js',
                './styles.css',
                './sw.js',
                '../images/staff.png',
                '../images/logo.jpg',
                '../images/exhibitor.png',


            ]);
        })
    );
});
