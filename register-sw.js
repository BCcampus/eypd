// (async function () {
//     'serviceWorker' in navigator && navigator.serviceWorker.register(`${_wordpressConfig.templateUrl}/sw.php`, {scope: '/'})
// })();

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register(`${_wordpressConfig.templateUrl}/sw.js`, {scope:`${_wordpressConfig.templateUrl}/`});
}