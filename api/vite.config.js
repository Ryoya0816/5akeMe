// /api/vite.config.ts
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    tailwindcss(),
  ],
  server: {
    host: true,            // コンテナ外から到達可
    port: 5174,            // composeの ports と合わせる
    strictPort: true,      // 他ポートへ勝手にずらさない
    hmr: {
      host: 'localhost',   // ブラウザが接続するホスト（http://localhost:8080で開く前提）
      port: 5174,
      protocol: 'ws',
    },
  },
})
