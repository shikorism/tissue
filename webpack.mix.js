const mix = require('laravel-mix');
require('laravel-mix-bundle-analyzer')

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

mix.js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/home.js', 'public/js')
    .js('resources/assets/js/user/profile.js', 'public/js/user')
    .js('resources/assets/js/user/stats.js', 'public/js/user')
    .js('resources/assets/js/setting/privacy.js', 'public/js/setting')
    .js('resources/assets/js/setting/import.js', 'public/js/setting')
    .js('resources/assets/js/setting/deactivate.js', 'public/js/setting')
    .ts('resources/assets/js/checkin.ts', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .autoload({
        'jquery': ['$', 'jQuery', 'window.jQuery']
    })
    .extract(['jquery', 'bootstrap'])
    .extract(['chart.js', 'chartjs-color', 'color-name', 'moment'], 'public/js/vendor/chart')
    .version();

if (process.argv.includes('-a')) {
    mix.bundleAnalyzer({analyzerMode: 'static'});
}
