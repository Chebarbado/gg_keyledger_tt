import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Montserrat', {
                    weights: [400, 500, 600, 700, 800, 900],
                    // без cyrillic кириллица шла системным шрифтом → «разные» шрифты рядом с латиницей
                    subsets: ['latin', 'latin-ext', 'cyrillic', 'cyrillic-ext'],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
