{{-- resources/views/errors/500.blade.php --}}
@extends('layouts.app')

@section('title', 'サーバーエラー - 5akeMe')

@section('content')
<div class="error-page">
    <style>
        .error-page {
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }

        .error-icon {
            font-size: 80px;
            margin-bottom: 24px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .error-code {
            font-size: 60px;
            font-weight: 800;
            color: var(--brand-main, #9c3f2e);
            line-height: 1;
            margin-bottom: 8px;
        }

        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main, #3f3f3f);
            margin-bottom: 16px;
        }

        .error-message {
            font-size: 16px;
            color: var(--text-sub, #8c6d57);
            line-height: 1.8;
            margin-bottom: 32px;
            max-width: 400px;
        }

        .error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
        }

        .error-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease-out;
        }

        .error-btn-primary {
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(156, 63, 46, 0.3);
        }

        .error-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(156, 63, 46, 0.4);
        }

        .error-btn-secondary {
            background: var(--bg-soft, #fff7ee);
            color: var(--brand-main, #9c3f2e);
            border: 1px solid var(--line-soft, #f1dfd0);
        }

        .error-btn-secondary:hover {
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .error-icon {
                font-size: 60px;
            }

            .error-code {
                font-size: 48px;
            }

            .error-title {
                font-size: 20px;
            }

            .error-message {
                font-size: 14px;
            }

            .error-btn {
                padding: 12px 24px;
                font-size: 14px;
            }

            .error-actions {
                flex-direction: column;
                width: 100%;
                max-width: 280px;
            }

            .error-btn {
                justify-content: center;
            }
        }
    </style>

    <div class="error-icon">🍶💥</div>

    <div class="error-code">500</div>

    <h1 class="error-title">おっと、何かがこぼれたみたい...</h1>

    <p class="error-message">
        サーバーで問題が発生しました。<br>
        しばらく待ってから、もう一度お試しください。<br>
        問題が続く場合はお問い合わせください。
    </p>

    <div class="error-actions">
        <a href="{{ route('top') }}" class="error-btn error-btn-primary">
            <span>🏠</span>
            <span>トップに戻る</span>
        </a>
        <a href="{{ route('contact') }}" class="error-btn error-btn-secondary">
            <span>📮</span>
            <span>お問い合わせ</span>
        </a>
    </div>
</div>
@endsection
