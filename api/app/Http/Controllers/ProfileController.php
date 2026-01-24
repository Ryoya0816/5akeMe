<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            
            // バリデーション済みデータを取得
            $validated = $request->validated();
            
            \Log::info('Profile update request', [
                'user_id' => $user->id,
                'has_avatar' => $request->hasFile('avatar'),
                'remove_avatar' => $request->input('remove_avatar'),
                'validated' => $validated,
            ]);
            
            // アバター削除リクエストの処理
            if ($request->has('remove_avatar') && $request->remove_avatar === '1') {
                // 古いアバターを削除（SNSログインの場合は削除しない）
                if ($user->avatar && !$user->isSocialLogin()) {
                    // ローカルストレージのアバターのみ削除
                    if (strpos($user->avatar, '/storage/') === 0) {
                        $oldAvatar = str_replace('/storage/', '', $user->avatar);
                        Storage::disk('public')->delete($oldAvatar);
                    }
                }
                $user->avatar = null;
            }
            
            // アバター画像のアップロード処理
            if ($request->hasFile('avatar')) {
                // 古いアバターを削除（SNSログインの場合は削除しない）
                if ($user->avatar && !$user->isSocialLogin()) {
                    // ローカルストレージのアバターのみ削除
                    if (strpos($user->avatar, '/storage/') === 0 || strpos($user->avatar, 'storage/') === 0) {
                        $oldAvatar = str_replace(['/storage/', 'storage/'], '', $user->avatar);
                        Storage::disk('public')->delete($oldAvatar);
                    }
                }
                
                // 新しいアバターを保存
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                // /storage/で始まるパスを保存（asset()で変換される）
                $user->avatar = '/storage/' . $avatarPath;
                
                \Log::info('Avatar uploaded', [
                    'path' => $user->avatar,
                    'storage_path' => $avatarPath,
                ]);
            }
            
            // その他のプロフィール情報を更新（avatarは除外）
            unset($validated['avatar']); // avatarは既に処理済みなので除外
            
            // nameとemailを個別に設定（fillだとisDirtyが正しく動作しない場合がある）
            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }
            if (isset($validated['email'])) {
                $oldEmail = $user->email;
                $user->email = $validated['email'];
                
                if ($oldEmail !== $user->email) {
                    $user->email_verified_at = null;
                }
            }

            // アバター変更フラグ
            $avatarWasChanged = $request->hasFile('avatar') || ($request->has('remove_avatar') && $request->remove_avatar === '1');
            
            // 変更があったか確認
            $hasChanges = $user->isDirty();
            
            // アバターが変更された場合、またはその他の変更がある場合は保存
            if ($hasChanges || $avatarWasChanged) {
                \Log::info('Saving user', [
                    'dirty' => $user->getDirty(),
                    'avatar_changed' => $avatarWasChanged,
                    'original_avatar' => $user->getOriginal('avatar'),
                    'current_avatar' => $user->avatar,
                ]);
                
                $user->save();
                
                // 保存後の値を再取得して確認
                $user->refresh();
                
                \Log::info('User saved successfully', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ]);
            } else {
                \Log::warning('No changes detected', [
                    'has_file' => $request->hasFile('avatar'),
                    'remove_avatar' => $request->input('remove_avatar'),
                    'isDirty' => $user->isDirty(),
                    'current_avatar' => $user->avatar,
                ]);
            }

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return Redirect::route('profile.edit')
                ->withErrors(['error' => 'プロフィールの更新に失敗しました: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
