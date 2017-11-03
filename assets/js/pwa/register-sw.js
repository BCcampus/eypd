// (async function () {
//     'serviceWorker' in navigator && navigator.serviceWorker.register(`${_wordpressConfig.templateUrl}/sw.php`, {scope: '/'})
// })();

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register(`${_wordpressConfig.templateUrl}/dist/scripts/pwa/sw.js`, {scope: '/'});
}