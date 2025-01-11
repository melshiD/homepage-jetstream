import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0', // Allows access from external devices or cloud environments
        port: 5173, // Default Vite port; you can change if needed
        strictPort: true, // Ensures the server always uses the specified port
        watch: {
            usePolling: true, // Useful for cloud environments where file system events might not be detected
        },
        hmr: {
            host: 'https://8000-idx-homepagejetstream-1736450925415.cluster-joak5ukfbnbyqspg4tewa33d24.cloudworkstations.dev/', // your domain or IP
        },
    },
});