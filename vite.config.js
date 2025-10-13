import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel([
            'resources/assets/sass/app.scss',
            'resources/assets/sass/agecheck.css',
            'resources/assets/js/app.ts',
            'resources/assets/js/agecheck.ts',
            'resources/assets/js/admin/rules.ts',
            'resources/assets/js/setting/privacy.ts',
            'resources/assets/js/setting/import.ts',
            'resources/assets/js/setting/deactivate.ts',
            'resources/assets/js/setting/tokens.ts',
            'resources/assets/js/setting/webhooks.ts',
            'resources/assets/js/setting/filter/tags.ts',
            'frontend/App.tsx',
        ]),
        react(),
        tailwindcss(),
    ],
});
