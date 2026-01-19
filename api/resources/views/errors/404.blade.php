{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.app')

@section('title', 'ページが見つかりません - 5akeMe')

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

        .error-mascot {
            width: 180px;
            height: auto;
            margin-bottom: 24px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-3deg); }
            50% { transform: translateY(-10px) rotate(3deg); }
        }

        .error-code {
            font-size: 80px;
            font-weight: 800;
            color: var(--brand-main, #9c3f2e);
            line-height: 1;
            margin-bottom: 8px;
            text-shadow: 2px 2px 0 var(--line-soft, #f1dfd0);
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

        .error-message .sake-icon {
            display: inline-block;
            animation: roll 2s ease-in-out infinite;
        }

        @keyframes roll {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
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

        .error-decoration {
            margin-top: 48px;
            font-size: 32px;
            opacity: 0.5;
        }

        @media (max-width: 640px) {
            .error-mascot {
                width: 140px;
            }

            .error-code {
                font-size: 60px;
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

    <img 
        src="{{ asset('images/mascot.png') }}" 
        alt="5akeMeマスコット - 迷子になったページ" 
        class="error-mascot"
        loading="lazy"
    >

    <div class="error-code">404</div>

    <h1 class="error-title">あれ？迷子になったみたい...</h1>

    <p class="error-message">
        お探しのページは、どこかへ転がっていったようです。<br>
        <span class="sake-icon">🍶</span><br>
        もしかして、ちょっと飲みすぎたかな？
    </p>

    <div class="error-actions">
        <a href="{{ route('top') }}" class="error-btn error-btn-primary">
            <span>🏠</span>
            <span>トップに戻る</span>
        </a>
        <a href="{{ route('diagnose') }}" class="error-btn error-btn-secondary">
            <span>🔍</span>
            <span>診断してみる</span>
        </a>
    </div>

    <div class="error-decoration">
        🍶 🍷 🍺 🥃 🍾
    </div>
</div>
@endsection
