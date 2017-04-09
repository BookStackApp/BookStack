const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/global.js', './public/js/common.js')
    .js('resources/assets/js/vues/vues.js', './public/js/vues.js')
    .sass('resources/assets/sass/styles.scss', 'public/css')
    .sass('resources/assets/sass/print-styles.scss', 'public/css')
    .sass('resources/assets/sass/export-styles.scss', 'public/css');
