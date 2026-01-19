{{-- resources/views/age_denied.blade.php --}}
@extends('layouts.app')

@section('title', 'ご利用いただけません - 5akeMe')

@section('content')
<div class="age-denied-page">
    <style>
        .age-denied-page {
            max-width: 500px;
            margin: 0 auto;
            padding: 60px 20px;
            text-align: center;
        }

        .age-denied-icon {
            font-size: 64px;
            margin-bottom: 24px;
            line-height: 1;
        }

        .age-denied-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 16px;
        }

        .age-denied-message {
            font-size: 15px;
            color: var(--text-main, #3f3f3f);
            margin-bottom: 32px;
            line-height: 1.7;
        }

        .age-denied-redirect {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 16px 24px;
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 12px;
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
        }

        .age-denied-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid var(--line-soft, #f1dfd0);
            border-top-color: var(--brand-main, #9c3f2e);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .age-denied-hashtag {
            margin-top: 24px;
            font-size: 13px;
            color: var(--text-sub, #8c6d57);
        }

        .age-denied-hashtag a {
            color: var(--brand-main, #9c3f2e);
            text-decoration: none;
            font-weight: 600;
        }

        .age-denied-hashtag a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .age-denied-page {
                padding: 40px 16px;
            }

            .age-denied-icon {
                font-size: 48px;
            }

            .age-denied-title {
                font-size: 20px;
            }
        }
    </style>

    <div class="age-denied-icon">🍵</div>
    
    <h1 class="age-denied-title">ごめんね、大人になってまた来てね！</h1>
    
    <p class="age-denied-message">
        5akeMeはお酒に関するサービスのため、<br>
        20歳未満の方はご利用いただけません。<br>
        大人になったらまた遊びに来てください🍶
    </p>

    <div class="age-denied-redirect">
        <span class="age-denied-spinner"></span>
        <span>3秒後に移動します...</span>
    </div>

    <p class="age-denied-hashtag">
        佐賀の魅力を発信中 👉 
        <a href="https://www.google.com/search?q=%23HelloSAGAworld" target="_blank" rel="noopener">
            #HelloSAGAworld
        </a>
    </p>
</div>

{{-- 3秒後に Google で「#HelloSAGAworld」を検索 --}}
<script>
    setTimeout(function() {
        window.location.href = "https://www.google.com/search?q=%23HelloSAGAworld";
    }, 3000);
</script>
@endsection
