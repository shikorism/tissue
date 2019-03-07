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
    .js('resources/assets/js/user/stats.js', 'public/js/user')
    .js('resources/assets/js/setting/privacy.js', 'public/js/setting')
    .js('resources/assets/js/checkin.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .autoload({
        'jquery': ['$', 'jQuery', 'window.jQuery']
    })
    .extract(['jquery', 'bootstrap'])
    .extract(['chart.js', 'moment'], 'public/js/vendor/chart')
    .version();

if (process.argv.includes('-a')) {
    mix.bundleAnalyzer({analyzerMode: 'static'});
}
