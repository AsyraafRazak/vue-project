import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
    plugins: [
        vue(),
        vueDevTools(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
        },
    },
    server: {
        watch: {
            ignored: ['**/.vs/**'],
        },
        proxy: {
            // Forwards /api requests to XAMPP during development
            '/api': {
                target: 'http://localhost',
                changeOrigin: true,
                rewrite: (path) => '/vue%20project' + path
            }
        }
    },
})