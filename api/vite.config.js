import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5174,
        strictPort: true,
        // Docker 内で動く Vite を、ブラウザが localhost:5174 で取得できるようにする
        origin: 'http://localhost:5174',
        cors: {
            origin: [
                'http://localhost:8082',
                'http://127.0.0.1:8082',
            ],
            credentials: true,
        },
    },
    // 本番ビルド最適化
    build: {
        // ファイルサイズを小さく
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,  // console.log削除
                drop_debugger: true, // debugger削除
            },
        },
        // チャンク分割（キャッシュ効率向上）
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                },
            },
        },
        // ソースマップは本番では無効
        sourcemap: false,
    },
});
