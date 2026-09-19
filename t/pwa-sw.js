const CACHE='armaghan-ux-v2-20260919b';
const CORE=['./index.htm','./shared-v2/catalog.css','./shared-v2/catalog-1.js','./shared-v2/catalog-2.js','./shared-v2/catalog-3.js','./shared-v2/catalog-4.js','./shared-v2/icon.svg','./08/index.htm','./09/index.htm','./10/index.htm'];
self.addEventListener('install',e=>{e.waitUntil(caches.open(CACHE).then(c=>c.addAll(CORE)).catch(()=>{}));self.skipWaiting()});
self.addEventListener('activate',e=>{e.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>k!==CACHE&&k.startsWith('armaghan-ux-')).map(k=>caches.delete(k)))));self.clients.claim()});
self.addEventListener('fetch',e=>{if(e.request.method!=='GET'||new URL(e.request.url).origin!==location.origin)return;e.respondWith(fetch(e.request).then(r=>{const copy=r.clone();caches.open(CACHE).then(c=>c.put(e.request,copy));return r}).catch(()=>caches.match(e.request).then(r=>r||caches.match('./10/index.htm'))))});
