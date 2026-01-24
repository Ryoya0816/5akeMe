<div class="profile-section">
    <h2 class="profile-section-title">パスワード変更</h2>
    <p class="profile-section-desc">アカウントのセキュリティのため、長くランダムなパスワードを使用してください。</p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="profile-form-group">
            <label for="update_password_current_password" class="profile-label">現在のパスワード</label>
            <input 
                id="update_password_current_password" 
                name="current_password" 
                type="password" 
                class="profile-input" 
                autocomplete="current-password"
            >
            @error('current_password', 'updatePassword')
                <div class="profile-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="profile-form-group">
            <label for="update_password_password" class="profile-label">新しいパスワード</label>
            <input 
                id="update_password_password" 
                name="password" 
                type="password" 
                class="profile-input" 
                autocomplete="new-password"
            >
            @error('password', 'updatePassword')
                <div class="profile-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="profile-form-group">
            <label for="update_password_password_confirmation" class="profile-label">新しいパスワード（確認）</label>
            <input 
                id="update_password_password_confirmation" 
                name="password_confirmation" 
                type="password" 
                class="profile-input" 
                autocomplete="new-password"
            >
            @error('password_confirmation', 'updatePassword')
                <div class="profile-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; align-items: center; gap: 16px; margin-top: 24px;">
            <button type="submit" class="profile-submit">保存</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    style="font-size: 14px; color: var(--text-sub);"
                >保存しました。</p>
            @endif
        </div>
    </form>
</div>
