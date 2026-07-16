import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // OMEGA-001A: loaded only on the Commerce product form
                'resources/js/product-image.js',
                // WEBSITE-002A: slim bundle for the public marketing site
                'resources/js/corporate.js',
            ],
            refresh: true,
        }),
    ],
});
