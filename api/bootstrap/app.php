<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // グローバル: セキュリティヘッダー（Web / API 共通）
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Web のみ: フォーム入力の制御文字除去（JSON API 本体はバリデーションで扱う）
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SanitizeInput::class,
            \App\Http\Middleware\CheckUserActive::class,
        ]);

        // ルートで ->middleware('age.verified') みたいに使えるように alias 登録
        $middleware->alias([
            'age.verified' => \App\Http\Middleware\AgeVerified::class,
        ]);

        // APIのレート制限を強化
        $middleware->throttleApi('60,1'); // 1分間に60リクエストまで
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
