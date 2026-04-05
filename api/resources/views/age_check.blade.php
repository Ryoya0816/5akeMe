{{-- resources/views/age_check.blade.php --}}
@extends('layouts.app')

@section('title', '年齢確認 - 5akeMe')
@section('description', '5akeMeは20歳以上の方を対象としたサービスです。年齢確認にご協力ください。')

@section('content')
<div class="age-check-page">
    <style>
        .age-check-page {
            max-width: 500px;
            margin: 0 auto;
            padding: 60px 20px;
            text-align: center;
        }

        .age-check-icon {
            display: flex;
            justify-content: center;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 24px;
        }

        .age-check-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 12px;
        }

        .age-check-subtitle {
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
            margin-bottom: 48px;
            line-height: 1.6;
        }

        /* ボタンエリア */
        .age-check-buttons {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-bottom: 32px;
        }

        /* 丸いYes/Noボタン */
        .age-btn {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all var(--transition-normal, 200ms ease-out);
            box-shadow: var(--shadow-md, 0 8px 24px rgba(0, 0, 0, 0.06));
        }

        .age-btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .age-btn-label {
            font-size: 18px;
        }

        .age-btn-sub {
            font-size: 11px;
            opacity: 0.8;
        }

        /* はいボタン（緑系） */
        .age-btn-yes {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            color: #ffffff;
        }

        .age-btn-yes:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 28px rgba(34, 197, 94, 0.4);
        }

        /* いいえボタン（グレー系） */
        .age-btn-no {
            background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
            color: #ffffff;
        }

        .age-btn-no:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 28px rgba(156, 163, 175, 0.4);
        }

        /* 注意書き */
        .age-check-note {
            font-size: 12px;
            color: var(--text-sub, #8c6d57);
            line-height: 1.7;
        }

        .age-check-note a {
            color: var(--brand-main, #9c3f2e);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .age-check-page {
                padding: 40px 16px;
            }

            .age-check-icon svg {
                width: 44px;
                height: 44px;
            }

            .age-check-title {
                font-size: 20px;
            }

            .age-check-buttons {
                gap: 16px;
            }

            .age-btn {
                width: 120px;
                height: 120px;
            }

            .age-btn-icon svg {
                width: 24px;
                height: 24px;
            }

            .age-btn-label {
                font-size: 16px;
            }

            .age-btn-sub {
                font-size: 10px;
            }
        }
    </style>

    <div class="age-check-icon"><x-svg-icon name="wine" size="56" /></div>
    
    <h1 class="age-check-title">あなたは20歳以上ですか？</h1>
    <p class="age-check-subtitle">
        5akeMeはお酒に関するサービスです。<br>
        20歳未満の方はご利用いただけません。
    </p>

    <div class="age-check-buttons">
        {{-- 「はい」ボタン --}}
        <form method="POST" action="{{ route('age.verify') }}">
            @csrf
            <button type="submit" class="age-btn age-btn-yes">
                <span class="age-btn-icon"><x-svg-icon name="check-circle" size="28" /></span>
                <span class="age-btn-label">はい</span>
                <span class="age-btn-sub">20歳以上です</span>
            </button>
        </form>

        {{-- 「いいえ」ボタン --}}
        <a href="{{ route('age.denied') }}" class="age-btn age-btn-no">
            <span class="age-btn-icon"><x-svg-icon name="x" size="28" /></span>
            <span class="age-btn-label">いいえ</span>
            <span class="age-btn-sub">20歳未満です</span>
        </a>
    </div>

    <p class="age-check-note">
        「はい」を押すと、年齢確認の記録がCookieに保存されます。<br>
        詳しくは<a href="{{ route('privacy') }}">プライバシーポリシー</a>をご覧ください。
    </p>
</div>
@endsection
