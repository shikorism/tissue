// eslint-disable-next-line @typescript-eslint/no-var-requires
const mix = require('laravel-mix');
require('laravel-mix-bundle-analyzer');

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

mix.ts('resources/assets/js/app.ts', 'public/js')
    .ts('resources/assets/js/home.ts', 'public/js')
    .ts('resources/assets/js/user/profile.ts', 'public/js/user')
    .ts('resources/assets/js/user/stats.ts', 'public/js/user')
    .ts('resources/assets/js/user/collections.tsx', 'public/js/user')
    .ts('resources/assets/js/setting/privacy.ts', 'public/js/setting')
    .ts('resources/assets/js/setting/import.ts', 'public/js/setting')
    .ts('resources/assets/js/setting/deactivate.ts', 'public/js/setting')
    .ts('resources/assets/js/setting/tokens.ts', 'public/js/setting')
    .ts('resources/assets/js/setting/webhooks.ts', 'public/js/setting')
    .ts('resources/assets/js/setting/filter/tags.ts', 'public/js/setting/filter')
    .ts('resources/assets/js/checkin.tsx', 'public/js')
    .ts('resources/assets/js/collect.tsx', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .extract(['jquery', 'bootstrap', 'react', 'react-dom', 'react-bootstrap'])
    .extract({ libraries: /chart\.js|@kurkle\/color|cal-heatmap|d3|d3-[a-z0-9-]+/ }, 'public/js/vendor/chart')
    .react()
    .version()
    .sourceMaps(false);

if (process.argv.includes('-a')) {
    mix.bundleAnalyzer({ analyzerMode: 'static' });
}
