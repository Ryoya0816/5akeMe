{{-- resources/views/contact_thanks.blade.php --}}
@extends('layouts.app')

@section('title', '送信完了 - 5akeMe')

@section('content')
<div class="thanks-page">
    <style>
        .thanks-page {
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }

        .thanks-icon {
            font-size: 64px;
            margin-bottom: 24px;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .thanks-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 16px;
        }

        .thanks-message {
            font-size: 16px;
            color: var(--text-sub, #8c6d57);
            line-height: 1.8;
            margin-bottom: 32px;
            max-width: 400px;
        }

        .thanks-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease-out;
            box-shadow: 0 4px 12px rgba(156, 63, 46, 0.3);
        }

        .thanks-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(156, 63, 46, 0.4);
        }

        @media (max-width: 640px) {
            .thanks-icon {
                font-size: 48px;
            }

            .thanks-title {
                font-size: 24px;
            }

            .thanks-message {
                font-size: 14px;
            }
        }
    </style>

    <div class="thanks-icon">✉️</div>

    <h1 class="thanks-title">送信完了しました！</h1>

    <p class="thanks-message">
        お問い合わせありがとうございます。<br>
        内容を確認の上、ご返信いたします。<br>
        しばらくお待ちください 🍶
    </p>

    <a href="{{ route('top') }}" class="thanks-btn">
        <span>🏠</span>
        <span>トップに戻る</span>
    </a>
</div>
@endsection
