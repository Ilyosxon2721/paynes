import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Разделяем большие библиотеки на отдельные chunks для лучшего кеширования
                    if (id.includes('node_modules')) {
                        if (id.includes('element-plus') && id.includes('@element-plus/icons-vue')) {
                            return 'element-plus-icons';
                        }
                        if (id.includes('element-plus')) {
                            return 'element-plus';
                        }
                        if (id.includes('vue-router') || id.includes('pinia')) {
                            return 'vue-vendor';
                        }
                        // Все остальные node_modules в отдельный chunk
                        return 'vendor';
                    }
                },
            },
        },
        // Увеличиваем лимит предупреждений до 600kb
        chunkSizeWarningLimit: 600,
        // Используем esbuild для минификации (быстрее и без дополнительных зависимостей)
        minify: 'esbuild',
    },
});
