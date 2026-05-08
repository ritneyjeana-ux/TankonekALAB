import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig(({ command }) => ({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
  server: {
    host: '127.0.0.1',
    port: 5173,
    strictPort: false, // ✅ kung busy ang 5173, kukuha ng ibang port
    cors: true,        // ✅ allow from Laravel (port 8000)
    hmr: {
      host: '127.0.0.1',
      protocol: 'ws',
    },
  },
  build: {
    sourcemap: command === 'serve', // ✅ mas madali i-debug kapag dev mode
  },
}));
