System.register([], function () {
    'use strict';
    return {
        setters: [], execute: function () {
            function a(a) {
                return '/' !== new URL(a.request.url).pathname
            }

            function b(a) {
                return new URL(a.request.url).hostname !== new URL(_wordpressConfig.templateUrl).hostname
            }

            function c(a) {
                return 'true' === new URL(a.request.url).searchParams.get('fragment')
            }

            function d(a) {
                return /(jpe?g|png|css|svg|js|woff)$/i.test(a.request.url) || a.request.url.endsWith('manifest.php')
            }

            function e(a) {
                return new URL(a.request.url).pathname.startsWith('/wp-content/plugins')
            }

            function f(a) {
                const b = new URL(a.request.url);
                return b.pathname.startsWith('/wp-') && !b.pathname.startsWith('/wp-content')
            }

            function g(a) {
                return new URL(a.request.url).searchParams.has('customize_changeset_uuid')
            }

            function h(a) {
                return 'POST' === a.request.method && '/wp-comments-post.php' === new URL(a.request.url).pathname
            }

            async function i(a, b) {
                const c = fetch(a, {credentials: 'include'}).catch(() => {
                }), d = caches.match(a);
                b.waitUntil(async function () {
                    const e = await j(a.url, b), f = await caches.open(e), g = await c, h = await d;
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

            async function j(a, b) {
                if (a.startsWith(_wordpressConfig.templateUrl)) return 'pwp';
                const c = await self.clients.get(b.clientId);
                return c && (a = c.url), `pwp_pathid_${new URL(a).pathname.split('/').filter((a) => !!a).join('_')}`
            }

            function k(a) {
                _bgSyncManager.supportsBackgroundSync && a.waitUntil(async function () {
                    const b = new URL(a.request.referrer);
                    a.respondWith(new Response(null, {
                        status: 302,
                        headers: {Location: b.pathname}
                    })), await _bgSyncManager.enqueue(a.request), await _bgSyncManager.trigger()
                }())
            }

            importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/transformstream.js`), importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/idb.js`), importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/pubsubhub.js`), importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/bg-sync-manager.js`), importScripts(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/analytics-sw.js`);
            self.oninstall = (a) => {
                a.waitUntil(async function () {
                    const a = await caches.open('pwp');
                    return await a.addAll([`${_wordpressConfig.templateUrl}/header.php?fragment=true`, `${_wordpressConfig.templateUrl}/?fragment=true`, `${_wordpressConfig.templateUrl}/footer.php?fragment=true`, `${_wordpressConfig.templateUrl}/dist/styles/pwa.css`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/import-polyfill.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/analytics.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/ric-polyfill.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/pubsubhub.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/router.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/pwp-view.js`, `${_wordpressConfig.templateUrl}/dist/scripts/pwa/pwp-spinner.js`].map((a) => new Request(a, {credentials: 'include'}))), await Promise.all(['https://www.google-analytics.com/analytics.js'].map((a) => new Request(a, {mode: 'no-cors'})).map(async (b) => await a.put(b, (await fetch(b))))), self.skipWaiting()
                }())
            }, self.onactivate = (a) => {
                a.waitUntil(self.clients.claim())
            }, self.onfetch = (j) => {
                if (isAnalyticsRequest(j)) return analytics(j);
                if (h(j)) return k(j);
                if (g(j) || f(j)) return;
                if (c(j) || d(j) || e(j) || b(j)) return j.respondWith(i(j.request, j));
                const l = new URL(j.request.url);
                l.searchParams.append('fragment', 'true'), l.searchParams.delete('loadimages');
                const m = [`${_wordpressConfig.templateUrl}/header.php?fragment=true`, l, `${_wordpressConfig.templateUrl}/footer.php?fragment=true`].map((a) => i(new Request(a), j)), {readable: n, writable: o} = new TransformStream;
                j.waitUntil(async function () {
                    a(j) && (m[0] = async function () {
                        const a = await m[0], b = await a.text();
                        return new Response(b.replace('class="hero', 'class="hero single'))
                    }());
                    for (const a of m) {
                        const b = await a;
                        await b.body.pipeTo(o, {preventClose: !0})
                    }
                    o.getWriter().close()
                }()), j.respondWith(new Response(n))
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
            }
        }
    }
});