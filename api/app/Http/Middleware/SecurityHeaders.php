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

        return $response;
    }
}
