importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/transformstream.js`), importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/idb.js`), importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/pubsubhub.js`), importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/bg-sync-manager.js`), importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/analytics-sw.js`);
const VERSION = '0.0.3407';
self.oninstall = (a) => {
    a.waitUntil(async function () {
        const a = await caches.open('pwp');
        return await a.addAll([`${_wordpressConfig.templateUrl}/header.php?fragment=true`, `${_wordpressConfig.templateUrl}/?fragment=true`, `${_wordpressConfig.templateUrl}/footer.php?fragment=true`, `${_wordpressConfig.templateUrl}/lazy.css`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/import-polyfill.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/analytics.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/ric-polyfill.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/pubsubhub.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/router.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/pwp-view.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/pwp-spinner.js`, `${_wordpressConfig.templateUrl}/components/fonts.css`, `${_wordpressConfig.templateUrl}/fonts/Catamaran-Black.woff`, `${_wordpressConfig.templateUrl}/fonts/Catamaran-Bold.woff`, `${_wordpressConfig.templateUrl}/fonts/Catamaran-Medium.woff`, `${_wordpressConfig.templateUrl}/fonts/Catamaran-Light.woff`].map((a) => new Request(a, {credentials: 'include'}))), await Promise.all(['https://www.google-analytics.com/analytics.js'].map((a) => new Request(a, {mode: 'no-cors'})).map(async (b) => await a.put(b, (await fetch(b))))), self.skipWaiting()
    }())
}, self.onactivate = (a) => {
    a.waitUntil(self.clients.claim())
}, self.onfetch = (a) => {
    if (isAnalyticsRequest(a)) return analytics(a);
    if (isCommentRequest(a)) return postComment(a);
    if (isCustomizerRequest(a) || isWpRequest(a)) return;
    if (isFragmentRequest(a) || isAssetRequest(a) || isPluginRequest(a) || isCrossOriginRequest(a)) return a.respondWith(staleWhileRevalidate(a.request, a));
    const b = new URL(a.request.url);
    b.searchParams.append('fragment', 'true'), b.searchParams.delete('loadimages');
    const c = [`${_wordpressConfig.templateUrl}/header.php?fragment=true`, b, `${_wordpressConfig.templateUrl}/footer.php?fragment=true`].map((b) => staleWhileRevalidate(new Request(b), a)), {readable: d, writable: e} = new TransformStream;
    a.waitUntil(async function () {
        needsSmallHeader(a) && (c[0] = async function () {
            const a = await c[0], b = await a.text();
            return new Response(b.replace('class="hero', 'class="hero single'))
        }());
        for (const a of c) {
            const b = await a;
            await b.body.pipeTo(e, {preventClose: !0})
        }
        e.getWriter().close()
    }()), a.respondWith(new Response(d))
}, self.onsync = (a) => {
    switch (a.tag) {
        case'test-tag-from-devtools':
        case'comment-sync':
        case'ga-sync':
            _bgSyncManager.process(a);
            break;
        default:
            console.error(`Unknown background sync: ${a.tag}`);
    }
};

function needsSmallHeader(a) {
    return '/' !== new URL(a.request.url).pathname
}

function isCrossOriginRequest(a) {
    return new URL(a.request.url).hostname !== new URL(_wordpressConfig.templateUrl).hostname
}

function isFragmentRequest(a) {
    return 'true' === new URL(a.request.url).searchParams.get('fragment')
}

function isAssetRequest(a) {
    return /(jpe?g|png|css|svg|js|woff)$/i.test(a.request.url) || a.request.url.endsWith('manifest.php')
}

function isPluginRequest(a) {
    return new URL(a.request.url).pathname.startsWith('/wp-content/plugins')
}

function isWpRequest(a) {
    const b = new URL(a.request.url);
    return b.pathname.startsWith('/wp-') && !b.pathname.startsWith('/wp-content')
}

function isCustomizerRequest(a) {
    return new URL(a.request.url).searchParams.has('customize_changeset_uuid')
}

function isCommentRequest(a) {
    return 'POST' === a.request.method && '/wp-comments-post.php' === new URL(a.request.url).pathname
}

async function staleWhileRevalidate(a, b) {
    const c = fetch(a, {credentials: 'include'}).catch(() => {
    }), d = caches.match(a);
    b.waitUntil(async function () {
        const e = await extractCacheName(a.url, b), f = await caches.open(e), g = await c, h = await d;
        if (g && h) {
            const b = g.headers.get('Etag') !== h.headers.get('Etag');
            b && (await _pubsubhub.dispatch('resource_update', {name: a.url}))
        }
        g && f.put(a, g.clone())
    }());
    const e = await d;
    if (e) return e.clone();
    const f = await c;
    if (f) return f.clone();
    throw new Error(`Neither network nor cache had a response for ${a.url}`)
}

async function extractCacheName(a, b) {
    if (a.startsWith(_wordpressConfig.templateUrl)) return 'pwp';
    const c = await self.clients.get(b.clientId);
    return c && (a = c.url), `pwp_pathid_${new URL(a).pathname.split('/').filter((a) => !!a).join('_')}`
}

function postComment(a) {
    _bgSyncManager.supportsBackgroundSync && a.waitUntil(async function () {
        const b = new URL(a.request.referrer);
        a.respondWith(new Response(null, {
            status: 302,
            headers: {Location: b.pathname}
        })), await _bgSyncManager.enqueue(a.request), await _bgSyncManager.trigger()
    }())
}