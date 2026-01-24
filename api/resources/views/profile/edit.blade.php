@extends('layouts.app')

@section('title', 'プロフィール編集 - マイページ')

@section('content')
<div class="mypage">
    <style>
        .mypage {
            min-height: 100vh;
            background: var(--bg-base, #fbf3e8);
            padding: 24px 16px 48px;
        }

        .mypage-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .mypage-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-sub);
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .mypage-back:hover {
            color: var(--brand-main);
        }

        .mypage-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 24px;
        }

        .profile-section {
            background: var(--card-bg, #fff);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 20px;
        }

        .profile-section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--brand-main);
            margin-bottom: 8px;
        }

        .profile-section-desc {
            font-size: 13px;
            color: var(--text-sub);
            margin-bottom: 24px;
        }

        .profile-form-group {
            margin-bottom: 20px;
        }

        .profile-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .profile-input {
            width: 100%;
            padding: 12px 14px;
            font-size: 15px;
            border: 1px solid var(--line-soft, #f1dfd0);
            border-radius: 10px;
            background: var(--bg-soft, #fff7ee);
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .profile-input:focus {
            outline: none;
            border-color: var(--brand-main);
            background: #fff;
        }

        .profile-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
        }

        /* アバターアップロード */
        .profile-avatar-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--line-soft);
        }

        .profile-avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--line-soft);
            background: var(--bg-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            overflow: hidden;
        }

        .profile-avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar-upload {
            flex: 1;
        }

        .profile-avatar-label {
            display: inline-block;
            padding: 10px 20px;
            background: var(--brand-main);
            color: #fff;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .profile-avatar-label:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .profile-avatar-input {
            display: none;
        }

        .profile-avatar-note {
            font-size: 12px;
            color: var(--text-sub);
            margin-top: 8px;
        }

        .profile-avatar-remove {
            font-size: 12px;
            color: var(--text-sub);
            text-decoration: underline;
            cursor: pointer;
            margin-top: 8px;
        }

        .profile-avatar-remove:hover {
            color: #dc2626;
        }

        .profile-submit {
            padding: 12px 28px;
            background: var(--brand-main);
            color: #fff;
            border: none;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            min-width: 120px;
            position: relative;
        }

        .profile-submit:hover:not(:disabled) {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .profile-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .profile-submit.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        .profile-success {
            padding: 12px 16px;
            background: #dcfce7;
            color: #166534;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .profile-email-note {
            font-size: 12px;
            color: var(--text-sub);
            margin-top: 4px;
        }

        .profile-email-verify {
            padding: 12px 16px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 10px;
            font-size: 13px;
            margin-top: 12px;
        }

        .profile-email-verify-link {
            color: var(--brand-main);
            text-decoration: underline;
            font-weight: 600;
        }

        .profile-email-verify-link:hover {
            color: var(--brand-text);
        }

        @media (max-width: 640px) {
            .profile-section {
                padding: 24px 20px;
            }

            .profile-avatar-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-avatar-preview {
                width: 80px;
                height: 80px;
                font-size: 32px;
            }
        }
    </style>

    <div class="mypage-container">
        <a href="{{ route('mypage') }}" class="mypage-back">
            ← マイページに戻る
        </a>

        <h1 class="mypage-title">プロフィール編集</h1>

        {{-- プロフィール情報更新フォーム --}}
        <div class="profile-section">
            <h2 class="profile-section-title">プロフィール情報</h2>
            <p class="profile-section-desc">アカウントのプロフィール情報とメールアドレスを更新できます。</p>

            @if (session('status') === 'profile-updated')
                <div class="profile-success">
                    プロフィールを更新しました。
                </div>
            @endif

            {{-- エラーメッセージ表示 --}}
            @if ($errors->any())
                <div style="padding: 12px 16px; background: #fee2e2; color: #dc2626; border-radius: 10px; font-size: 14px; margin-bottom: 20px;">
                    <strong>エラーが発生しました：</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- デバッグ情報（開発環境のみ） --}}
            @if (!app()->isProduction() && $errors->any())
                <div style="padding: 12px 16px; background: #f3f4f6; color: #374151; border-radius: 10px; font-size: 12px; margin-bottom: 20px; font-family: monospace;">
                    <strong>デバッグ情報：</strong><br>
                    Request Method: {{ request()->method() }}<br>
                    Has Avatar File: {{ request()->hasFile('avatar') ? 'Yes' : 'No' }}<br>
                    Remove Avatar: {{ request()->input('remove_avatar', 'Not set') }}<br>
                    Current Avatar: {{ $user->avatar ?? 'None' }}
                </div>
            @endif

            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                {{-- アバターアップロード --}}
                <div class="profile-avatar-section">
                    <div class="profile-avatar-preview" id="avatarPreview">
                        @if($user->avatar)
                            <img src="{{ strpos($user->avatar, 'http') === 0 ? $user->avatar : asset($user->avatar) }}" alt="{{ $user->name }}">
                        @else
                            🍶
                        @endif
                    </div>
                    <div class="profile-avatar-upload">
                        <label for="avatar" class="profile-avatar-label">
                            📷 アイコンを変更
                        </label>
                        <input 
                            type="file" 
                            id="avatar" 
                            name="avatar" 
                            class="profile-avatar-input"
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                            onchange="previewAvatar(this)"
                        >
                        <p class="profile-avatar-note">
                            JPEG、PNG、GIF、WebP形式（最大2MB）
                        </p>
                        @if($user->avatar && !$user->isSocialLogin())
                            <label class="profile-avatar-remove" onclick="setRemoveAvatar()">
                                アイコンを削除
                            </label>
                            <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">
                        @endif
                        @error('avatar')
                            <div class="profile-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ニックネーム --}}
                <div class="profile-form-group">
                    <label for="name" class="profile-label">ニックネーム</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="profile-input" 
                        value="{{ old('name', $user->name) }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                    >
                    @error('name')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- メールアドレス --}}
                <div class="profile-form-group">
                    <label for="email" class="profile-label">メールアドレス</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="profile-input" 
                        value="{{ old('email', $user->email) }}" 
                        required 
                        autocomplete="username"
                    >
                    @error('email')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                    <p class="profile-email-note">
                        @if($user->isSocialLogin())
                            SNSログインのため、メールアドレスは変更できません。
                        @else
                            メールアドレスを変更すると、確認メールが送信されます。
                        @endif
                    </p>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="profile-email-verify">
                            <p>
                                メールアドレスが確認されていません。
                                <button form="send-verification" class="profile-email-verify-link">
                                    確認メールを再送信
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p style="margin-top: 8px; font-size: 12px;">
                                    新しい確認リンクをメールアドレスに送信しました。
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div style="display: flex; align-items: center; gap: 16px; margin-top: 24px;">
                    <button type="submit" class="profile-submit" id="submitBtn">
                        <span id="submitText">保存</span>
                    </button>
                </div>
            </form>

            <script>
                // フォーム送信時の処理
                const form = document.querySelector('form[action="{{ route('profile.update') }}"]');
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');
                
                if (form && submitBtn) {
                    form.addEventListener('submit', function(e) {
                        const formData = new FormData(this);
                        
                        // デバッグログ
                        console.log('Form submitting:', {
                            name: formData.get('name'),
                            email: formData.get('email'),
                            avatar: formData.get('avatar')?.name || 'なし',
                            remove_avatar: formData.get('remove_avatar'),
                            _method: formData.get('_method'),
                        });
                        
                        // ローディング状態を有効化
                        submitBtn.disabled = true;
                        submitBtn.classList.add('loading');
                        submitText.textContent = '保存中...';
                        
                        // フォーム送信を続行（デフォルトの動作）
                    });
                }
            </script>
        </div>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        {{-- パスワード変更フォーム --}}
        @include('profile.partials.update-password-form')

        {{-- アカウント削除フォーム --}}
        @include('profile.partials.delete-user-form')
    </div>

    <script>
        // アバタープレビュー機能
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // アバター削除機能
        function setRemoveAvatar() {
            if (confirm('アイコンを削除しますか？')) {
                const preview = document.getElementById('avatarPreview');
                preview.innerHTML = '🍶';
                const input = document.getElementById('avatar');
                input.value = '';
                const removeInput = document.getElementById('remove_avatar');
                if (removeInput) {
                    removeInput.value = '1';
                }
            }
        }
    </script>
</div>
@endsection
