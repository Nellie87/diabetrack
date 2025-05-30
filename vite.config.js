import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  resolve: {
    alias: {
        'chart.js': 'chart.js/auto',
 // Ensure the correct path to Chart.js module
    },
  },
  plugins: [
    laravel({
      input: 'resources/js/app.js', // Adjust based on your Laravel project setup
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
});
