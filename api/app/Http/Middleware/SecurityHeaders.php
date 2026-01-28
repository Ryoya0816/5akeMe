<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * セキュリティ関連のHTTPヘッダーを追加
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // XSS対策: ブラウザのXSSフィルターを有効化
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // クリックジャッキング対策: iframeへの埋め込みを禁止
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // MIMEタイプスニッフィング対策
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer情報の制御
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 権限ポリシー（不要な機能を無効化）
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // 本番環境ではHSTSを有効化（HTTPSを強制）
        if (app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content Security Policy（XSS対策の強化）
        $styleSrc = ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com', 'https://fonts.bunny.net'];
        if (!app()->isProduction()) {
            // 開発時: Viteのスタイル用
            $styleSrc[] = 'http://localhost:5174';
            $styleSrc[] = 'http://127.0.0.1:5174';
        }
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            'style-src ' . implode(' ', $styleSrc),
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data:",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' ws://localhost:* http://localhost:* wss://localhost:*",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
