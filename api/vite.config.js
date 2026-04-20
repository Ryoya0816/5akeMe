import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const port = Number(process.env.VITE_PORT || 5175);

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    host: '0.0.0.0',
    port,
    strictPort: true,
    hmr: {
      host: 'localhost',
      port,
    },
  },
});
