<?php

/**
 * HTTP セキュリティヘッダー・CSP の単一ソース。
 * 本番/開発の差分は app()->isProduction() と csp_non_production のマージで表現する。
 */
return [

    'referrer_policy' => 'strict-origin-when-cross-origin',

    'permissions_policy' => 'camera=(), microphone=(), geolocation=()',

    /*
    |--------------------------------------------------------------------------
    | Content-Security-Policy（directive => トークン配列）
    |--------------------------------------------------------------------------
    | 最終的に "directive token1 token2" 形式で結合される。
    */
    'csp' => [
        'default-src' => ["'self'"],
        'script-src' => [
            "'self'",
            "'unsafe-inline'",
            "'unsafe-eval'",
            'https://cdn.jsdelivr.net',
        ],
        'style-src' => [
            "'self'",
            "'unsafe-inline'",
            'https://fonts.googleapis.com',
            'https://fonts.bunny.net',
        ],
        'font-src' => ["'self'", 'https://fonts.gstatic.com', 'https://fonts.bunny.net', 'data:'],
        'img-src' => ["'self'", 'data:', 'https:', 'blob:'],
        'connect-src' => [
            "'self'",
            'https://cdn.jsdelivr.net',
        ],
        'frame-ancestors' => ["'self'"],
        'form-action' => [
            "'self'",
            'https://formsubmit.co',
        ],
        'base-uri' => ["'self'"],
    ],

    /*
    |--------------------------------------------------------------------------
    | 非本番のみ CSP に追加するトークン（Vite dev 等）
    |--------------------------------------------------------------------------
    */
    'csp_non_production' => [
        'style-src' => [
            'http://localhost:5174',
            'http://127.0.0.1:5174',
        ],
        'script-src' => [
            'http://localhost:5174',
            'http://127.0.0.1:5174',
        ],
        'connect-src' => [
            'ws://localhost:*',
            'http://localhost:*',
            'wss://localhost:*',
        ],
    ],
];
