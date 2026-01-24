<div class="profile-section" style="border: 2px solid #fee2e2; background: #fef2f2;">
    <h2 class="profile-section-title" style="color: #dc2626;">アカウント削除</h2>
    <p class="profile-section-desc" style="color: #991b1b;">
        アカウントを削除すると、すべてのリソースとデータが永久に削除されます。アカウントを削除する前に、保持したいデータや情報をダウンロードしてください。
    </p>

    <button
        type="button"
        onclick="document.getElementById('delete-account-modal').style.display='block'"
        style="padding: 12px 24px; background: #dc2626; color: #fff; border: none; border-radius: 999px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
        onmouseover="this.style.opacity='0.9'"
        onmouseout="this.style.opacity='1'"
    >
        アカウントを削除
    </button>

    {{-- モーダル --}}
    <div id="delete-account-modal" style="display: none; position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
        <div style="background: #fff; border-radius: 20px; padding: 32px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <h2 style="font-size: 20px; font-weight: 700; color: var(--text-main); margin-bottom: 12px;">
                アカウントを削除してもよろしいですか？
            </h2>

            <p style="font-size: 14px; color: var(--text-sub); margin-bottom: 24px;">
                アカウントを削除すると、すべてのリソースとデータが永久に削除されます。アカウントを永久に削除することを確認するには、パスワードを入力してください。
            </p>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="profile-form-group">
                    <label for="password" class="profile-label">パスワード</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="profile-input"
                        placeholder="パスワードを入力"
                        required
                    >
                    @error('password', 'userDeletion')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button
                        type="button"
                        onclick="document.getElementById('delete-account-modal').style.display='none'"
                        style="padding: 10px 20px; background: var(--bg-soft); color: var(--text-main); border: 1px solid var(--line-soft); border-radius: 999px; font-size: 14px; font-weight: 600; cursor: pointer;"
                    >
                        キャンセル
                    </button>

                    <button
                        type="submit"
                        style="padding: 10px 20px; background: #dc2626; color: #fff; border: none; border-radius: 999px; font-size: 14px; font-weight: 600; cursor: pointer;"
                    >
                        アカウントを削除
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // モーダル外クリックで閉じる
        document.getElementById('delete-account-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    </script>
</div>
