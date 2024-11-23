import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel([
            'resources/assets/sass/app.scss',
            'resources/assets/js/app.ts',
            'resources/assets/js/home.ts',
            'resources/assets/js/admin/rules.ts',
            'resources/assets/js/user/profile.ts',
            'resources/assets/js/user/stats.ts',
            'resources/assets/js/user/collections.tsx',
            'resources/assets/js/setting/privacy.ts',
            'resources/assets/js/setting/import.ts',
            'resources/assets/js/setting/deactivate.ts',
            'resources/assets/js/setting/tokens.ts',
            'resources/assets/js/setting/webhooks.ts',
            'resources/assets/js/setting/filter/tags.ts',
            'resources/assets/js/checkin.tsx',
            'resources/assets/js/collect.tsx',
        ]),
        react(),
    ],
});
