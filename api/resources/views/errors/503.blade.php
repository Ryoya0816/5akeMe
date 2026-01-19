{{-- resources/views/errors/503.blade.php --}}
@extends('layouts.app')

@section('title', 'メンテナンス中 - 5akeMe')

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
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

        .error-note {
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 12px;
            padding: 16px 24px;
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
        }

        .error-social {
            margin-top: 24px;
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        .error-social-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 999px;
            color: var(--text-main, #3f3f3f);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease-out;
        }

        .error-social-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

            .error-note {
                font-size: 13px;
                padding: 12px 16px;
            }

            .error-social {
                flex-direction: column;
            }
        }
    </style>

    <div class="error-icon">🔧</div>

    <div class="error-code">503</div>

    <h1 class="error-title">ただいまメンテナンス中です</h1>

    <p class="error-message">
        より良いサービスをお届けするため、<br>
        現在メンテナンスを行っています。<br>
        しばらくお待ちください 🍶
    </p>

    <div class="error-note">
        最新情報はSNSでお知らせします
    </div>

    <div class="error-social">
        <a 
            href="https://www.instagram.com/hello.saga.world/" 
            class="error-social-link"
            target="_blank" 
            rel="noopener noreferrer"
        >
            📷 Instagram
        </a>
        <a 
            href="https://note.com/hello_sagaworld" 
            class="error-social-link"
            target="_blank" 
            rel="noopener noreferrer"
        >
            📝 NOTE
        </a>
    </div>
</div>
@endsection
