{{-- resources/views/contact.blade.php --}}
@extends('layouts.app')

@section('title', 'お問い合わせ - 5akeMe')
@section('description', '5akeMeへのご質問・ご要望・不具合報告など、お気軽にお問い合わせください。')
@section('og_title', 'お問い合わせ - 5akeMe')
@section('og_description', '5akeMeへのお問い合わせはこちらから。')

@section('content')
<div class="contact-page">
    <style>
        .contact-page {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .contact-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .contact-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 8px;
        }

        .contact-subtitle {
            font-size: 14px;
            color: var(--text-sub, #8c6d57);
            line-height: 1.6;
        }

        .contact-form {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 16px;
            padding: 32px;
            box-shadow: var(--shadow, 0 10px 20px rgba(0, 0, 0, 0.06));
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group:last-of-type {
            margin-bottom: 32px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main, #3f3f3f);
            margin-bottom: 8px;
        }

        .form-label-optional {
            font-weight: 400;
            color: var(--text-sub, #8c6d57);
            font-size: 12px;
            margin-left: 8px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            color: var(--text-main, #3f3f3f);
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 12px;
            transition: all 0.2s ease-out;
            font-family: inherit;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--brand-main, #9c3f2e);
            box-shadow: 0 0 0 3px rgba(156, 63, 46, 0.1);
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: var(--text-sub, #8c6d57);
            opacity: 0.6;
        }

        .form-textarea {
            min-height: 160px;
            resize: vertical;
        }

        .form-error {
            display: block;
            font-size: 12px;
            color: #dc2626;
            margin-top: 6px;
            min-height: 18px;
        }

        .form-counter {
            display: block;
            font-size: 12px;
            color: var(--text-sub, #8c6d57);
            text-align: right;
            margin-top: 4px;
        }

        .form-input--error,
        .form-textarea--error {
            border-color: #dc2626 !important;
            background: #fef2f2 !important;
        }

        .form-input--error:focus,
        .form-textarea--error:focus {
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
        }

        .form-submit {
            width: 100%;
            padding: 16px 32px;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            background: var(--brand-main, #9c3f2e);
            border: none;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s ease-out;
            box-shadow: 0 4px 12px rgba(156, 63, 46, 0.3);
        }

        .form-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(156, 63, 46, 0.4);
        }

        .form-submit:active {
            transform: translateY(0);
        }

        .contact-note {
            margin-top: 24px;
            padding: 16px;
            background: var(--bg-soft, #fff7ee);
            border-radius: 12px;
            font-size: 13px;
            color: var(--text-sub, #8c6d57);
            line-height: 1.6;
            text-align: center;
        }

        .contact-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            padding: 12px 24px;
            background: var(--bg-soft, #fff7ee);
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 999px;
            color: var(--brand-main, #9c3f2e);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease-out;
        }

        .contact-back:hover {
            background: var(--brand-main, #9c3f2e);
            color: #ffffff;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .contact-page {
                padding: 24px 16px;
            }

            .contact-title {
                font-size: 24px;
            }

            .contact-form {
                padding: 24px 20px;
            }

            .form-input,
            .form-textarea {
                padding: 12px 14px;
                font-size: 16px; /* prevent iOS zoom */
            }
        }
    </style>

    <header class="contact-header">
        <div class="contact-icon">📮</div>
        <h1 class="contact-title">お問い合わせ</h1>
        <p class="contact-subtitle">
            ご質問・ご要望・不具合報告など<br>
            お気軽にお問い合わせください
        </p>
    </header>

    <form 
        class="contact-form" 
        action="https://formsubmit.co/tsurutsuru0816@gmail.com" 
        method="POST"
    >
        <!-- FormSubmit設定 -->
        <input type="hidden" name="_subject" value="【5akeMe】お問い合わせがありました">
        <input type="hidden" name="_captcha" value="false">
        <input type="hidden" name="_next" value="{{ url('/contact/thanks') }}">
        <input type="text" name="_honey" style="display:none">

        <div class="form-group">
            <label for="name" class="form-label">
                お名前
                <span class="form-label-optional">（任意）</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-input"
                placeholder="例: 山田太郎"
                maxlength="100"
                autocomplete="name"
            >
            <span class="form-error" id="name-error"></span>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">
                メールアドレス
            </label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-input"
                placeholder="例: example@email.com"
                required
                maxlength="255"
                autocomplete="email"
                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
            >
            <span class="form-error" id="email-error"></span>
        </div>

        <div class="form-group">
            <label for="subject" class="form-label">
                件名
            </label>
            <input 
                type="text" 
                id="subject" 
                name="subject" 
                class="form-input"
                placeholder="例: 診断結果について"
                required
                maxlength="200"
            >
            <span class="form-error" id="subject-error"></span>
        </div>

        <div class="form-group">
            <label for="message" class="form-label">
                お問い合わせ内容
            </label>
            <textarea 
                id="message" 
                name="message" 
                class="form-textarea"
                placeholder="お問い合わせ内容をご記入ください"
                required
                maxlength="5000"
                minlength="10"
            ></textarea>
            <span class="form-error" id="message-error"></span>
            <span class="form-counter"><span id="message-count">0</span> / 5000</span>
        </div>

        <button type="submit" class="form-submit" id="submit-btn">
            送信する 📨
        </button>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.contact-form');
        const emailInput = document.getElementById('email');
        const messageInput = document.getElementById('message');
        const messageCount = document.getElementById('message-count');
        const submitBtn = document.getElementById('submit-btn');

        // メッセージ文字数カウント
        messageInput.addEventListener('input', function() {
            messageCount.textContent = this.value.length;
        });

        // メールアドレスのリアルタイムバリデーション
        emailInput.addEventListener('blur', function() {
            const emailError = document.getElementById('email-error');
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            if (this.value && !emailPattern.test(this.value)) {
                emailError.textContent = '有効なメールアドレスを入力してください';
                this.classList.add('form-input--error');
            } else {
                emailError.textContent = '';
                this.classList.remove('form-input--error');
            }
        });

        // フォーム送信時のバリデーション
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const errors = [];

            // メールアドレスチェック
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(emailInput.value)) {
                document.getElementById('email-error').textContent = '有効なメールアドレスを入力してください';
                emailInput.classList.add('form-input--error');
                isValid = false;
            }

            // メッセージ長チェック
            if (messageInput.value.length < 10) {
                document.getElementById('message-error').textContent = '10文字以上入力してください';
                messageInput.classList.add('form-input--error');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            // 二重送信防止
            submitBtn.disabled = true;
            submitBtn.textContent = '送信中... ⏳';
        });
    });
    </script>

    <p class="contact-note">
        ※ 通常、2〜3営業日以内にご返信いたします。<br>
        お急ぎの場合はSNSからもお問い合わせいただけます。
    </p>

    <a href="{{ route('top') }}" class="contact-back">
        <span>←</span>
        <span>トップに戻る</span>
    </a>
</div>
@endsection
