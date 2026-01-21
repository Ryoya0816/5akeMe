@extends('layouts.app')

@section('title', 'ログイン - 5akeMe')

@section('content')
<div class="auth-page">
    <style>
        .auth-page {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: var(--bg-base, #fbf3e8);
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
            background: var(--card-bg, #fff);
            border-radius: 24px;
            padding: 32px 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .auth-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin: 0;
        }

        .auth-subtitle {
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
            margin-top: 8px;
        }

        /* SNSボタン */
        .auth-social {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .auth-social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .auth-social-btn svg {
            width: 20px;
            height: 20px;
        }

        .auth-social-google {
            background: #fff;
            color: #333;
            border: 1px solid #ddd;
        }

        .auth-social-google:hover {
            background: #f8f8f8;
            border-color: #ccc;
        }

        .auth-social-line {
            background: #00B900;
            color: #fff;
        }

        .auth-social-line:hover {
            background: #00a000;
        }

        .auth-social-twitter {
            background: #000;
            color: #fff;
        }

        .auth-social-twitter:hover {
            background: #333;
        }

        /* 区切り線 */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
        }

        .auth-divider-line {
            flex: 1;
            height: 1px;
            background: var(--line-soft, #f1dfd0);
        }

        .auth-divider-text {
            font-size: 12px;
            color: var(--text-sub, #8c6d57);
        }

        /* フォーム */
        .auth-form-group {
            margin-bottom: 16px;
        }

        .auth-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main, #3f3f3f);
            margin-bottom: 6px;
        }

        .auth-input {
            width: 100%;
            padding: 12px 14px;
            font-size: 15px;
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 10px;
            background: var(--bg-soft, #fff7ee);
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .auth-input:focus {
            outline: none;
            border-color: var(--brand-main, #9c3f2e);
            background: #fff;
        }

        .auth-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
        }

        .auth-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text-main, #3f3f3f);
        }

        .auth-remember input {
            width: 18px;
            height: 18px;
            accent-color: var(--brand-main, #9c3f2e);
        }

        .auth-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 24px;
        }

        .auth-forgot {
            font-size: 13px;
            color: var(--text-sub, #8c6d57);
            text-decoration: none;
        }

        .auth-forgot:hover {
            color: var(--brand-main, #9c3f2e);
            text-decoration: underline;
        }

        .auth-submit {
            padding: 12px 28px;
            background: var(--brand-main, #9c3f2e);
            color: #fff;
            border: none;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .auth-submit:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--line-soft, #f1dfd0);
        }

        .auth-footer-text {
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
        }

        .auth-footer-link {
            color: var(--brand-main, #9c3f2e);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer-link:hover {
            text-decoration: underline;
        }

        .auth-alert {
            padding: 12px 16px;
            background: #dcfce7;
            color: #166534;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .auth-alert-error {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>

    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">🍶</div>
            <h1 class="auth-title">ログイン</h1>
            <p class="auth-subtitle">5akeMeへようこそ</p>
        </div>

        {{-- セッションステータス --}}
        @if (session('status'))
            <div class="auth-alert">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="auth-alert auth-alert-error">{{ session('error') }}</div>
        @endif

        {{-- SNSログイン --}}
        <div class="auth-social">
            <a href="{{ route('auth.social.redirect', 'google') }}" class="auth-social-btn auth-social-google">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Googleでログイン
            </a>
            <a href="{{ route('auth.social.redirect', 'line') }}" class="auth-social-btn auth-social-line">
                <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.105.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                </svg>
                LINEでログイン
            </a>
            <a href="{{ route('auth.social.redirect', 'twitter') }}" class="auth-social-btn auth-social-twitter">
                <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                X（Twitter）でログイン
            </a>
        </div>

        <div class="auth-divider">
            <div class="auth-divider-line"></div>
            <span class="auth-divider-text">または</span>
            <div class="auth-divider-line"></div>
        </div>

        {{-- メールログインフォーム --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="auth-form-group">
                <label for="email" class="auth-label">メールアドレス</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    class="auth-input" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                >
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-form-group">
                <label for="password" class="auth-label">パスワード</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    class="auth-input" 
                    required
                >
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <label class="auth-remember">
                <input type="checkbox" name="remember">
                ログイン状態を保持する
            </label>

            <div class="auth-actions">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-forgot">
                        パスワードを忘れた？
                    </a>
                @endif
                <button type="submit" class="auth-submit">ログイン</button>
            </div>
        </form>

        <div class="auth-footer">
            <p class="auth-footer-text">
                アカウントをお持ちでない方は
                <a href="{{ route('register') }}" class="auth-footer-link">新規登録</a>
            </p>
        </div>
    </div>
</div>
@endsection
