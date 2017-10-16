let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */
const assets = 'assets';
const dist = 'dist';
const node = 'node_modules';
const temp = 'templates';
const plugin = 'plugins';

mix.setPublicPath(dist);

// BrowserSync
mix.browserSync({
    host: 'localhost',
    proxy: 'https://secure.test.ca/pd',
    port: 3000,
    files: [
        `${temp}/**/*.php`,
        `${plugin}/**/*.php`,
        `${assets}/**/*.php`,
        `*.php`,
        `${dist}/**/*.css`,
        `${dist}/**/*.js`,
    ],
});

// Assets
mix.copy(`${assets}/fonts`, `${dist}/fonts`)
    .copy(`${node}/bootstrap/fonts`, `${dist}/fonts`)
    .copy(`${node}/bootstrap/dist/css/bootstrap.min.css`, `${dist}/styles`)
    .copy(`${assets}/images`, `${dist}/images`);

// compiled Javascript
mix.js(`${node}/bootstrap/dist/js/bootstrap.min.js`, `${dist}/scripts`)
    .js(`${node}/bootstrap/js/popover.js`, `${dist}/scripts`)
    .js(`${node}/bootstrap/js/tooltip.js`, `${dist}/scripts`)
    .js(`${assets}/js/tabs.js`, `${dist}/scripts`)
    .js(`${assets}/js/initpopover.js`, `${dist}/scripts`)
    .js(`${assets}/js/jquery.tinyscrollbar.min.js`, `${dist}/scripts`)
    .js(`${assets}/js/events-manager.js`, `${dist}/scripts`)
    .js(`${assets}/js/markerclusterer.js`, `${dist}/scripts`)

// Sass
mix.sass(`${assets}/styles/main.scss`, `${dist}/styles/main.css`)
    .sass(`${assets}/styles/login.scss`, `${dist}/styles/login.css`)
    .sass(`${assets}/styles/admin.scss`, `${dist}/styles/admin.css`)
    .sass(`${assets}/styles/event.scss`, `${dist}/styles/event.css`)
    .sass(`${assets}/styles/media.scss`, `${dist}/styles/media.css`)
    .sass(`${assets}/styles/tinymce.scss`, `${dist}/styles/tinymce.css`)

// Options
mix.options({
    processCssUrls: false,
});

// Hash and version files in production.
if (mix.inProduction()) {
    mix.version();
}

// Full API
// mix.js(src, output);
// mix.react(src, output); <-- Identical to mix.js(), but registers React Babel compilation.
// mix.ts(src, output); <-- Requires tsconfig.json to exist in the same folder as webpack.mix.js
// mix.extract(vendorLibs);
// mix.sass(src, output);
// mix.standaloneSass('src', output); <-- Faster, but isolated from Webpack.
// mix.fastSass('src', output); <-- Alias for mix.standaloneSass().
// mix.less(src, output);
// mix.stylus(src, output);
// mix.postCss(src, output, [require('postcss-some-plugin')()]);
// mix.browserSync('my-site.dev');
// mix.combine(files, destination);
// mix.babel(files, destination); <-- Identical to mix.combine(), but also includes Babel compilation.
// mix.copy(from, to);
// mix.copyDirectory(fromDir, toDir);
// mix.minify(file);
// mix.sourceMaps(); // Enable sourcemaps
// mix.version(); // Enable versioning.
// mix.disableNotifications();
// mix.setPublicPath('path/to/public');
// mix.setResourceRoot('prefix/for/resource/locators');
// mix.autoload({}); <-- Will be passed to Webpack's ProvidePlugin.
// mix.webpackConfig({}); <-- Override webpack.config.js, without editing the file directly.
// mix.then(function () {}) <-- Will be triggered each time Webpack finishes building.
// mix.options({
//   extractVueStyles: false, // Extract .vue component styling to file, rather than inline.
//   processCssUrls: true, // Process/optimize relative stylesheet url()'s. Set to false, if you don't want them touched.
//   purifyCss: false, // Remove unused CSS selectors.
//   uglify: {}, // Uglify-specific options. https://webpack.github.io/docs/list-of-plugins.html#uglifyjsplugin
//   postCss: [] // Post-CSS options: https://github.com/postcss/postcss/blob/master/docs/plugins.md
// });
