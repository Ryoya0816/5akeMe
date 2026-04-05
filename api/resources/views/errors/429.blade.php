{{-- resources/views/errors/429.blade.php --}}
@extends('layouts.app')

@section('title', 'リクエスト過多 - 5akeMe')

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
            display: flex;
            justify-content: center;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 24px;
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

        .error-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            border-radius: var(--radius-full, 999px);
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all var(--transition-normal, 200ms ease-out);
            box-shadow: 0 4px 12px rgba(156, 63, 46, 0.3);
        }

        .error-btn-icon {
            display: flex;
            align-items: center;
        }

        .error-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(156, 63, 46, 0.4);
        }

        @media (max-width: 640px) {
            .error-icon svg {
                width: 56px;
                height: 56px;
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
        }
    </style>

    <div class="error-icon"><x-svg-icon name="coffee" size="72" /></div>

    <div class="error-code">429</div>

    <h1 class="error-title">ちょっと飲みすぎかも...？</h1>

    <p class="error-message">
        リクエストが多すぎます。<br>
        少し休憩してから、もう一度お試しください。<br>
        1分ほどお待ちいただければ大丈夫です。
    </p>

    <a href="{{ route('top') }}" class="error-btn">
        <span class="error-btn-icon"><x-svg-icon name="home" size="18" /></span>
        <span>トップに戻る</span>
    </a>
</div>
@endsection
