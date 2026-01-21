<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * 対応しているプロバイダー
     */
    protected array $providers = ['google', 'line', 'twitter'];

    /**
     * SNSログインへリダイレクト
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login')->with('error', '無効なログイン方法です。');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'このログイン方法は準備中です。');
        }
    }

    /**
     * SNSからのコールバック処理
     */
    public function callback(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login')->with('error', '無効なログイン方法です。');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'ログインに失敗しました。もう一度お試しください。');
        }

        // 既存ユーザーを検索（同じプロバイダー＆プロバイダーID）
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            // メールアドレスで既存ユーザーを検索
            if ($socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())->first();
                
                if ($user) {
                    // 既存アカウントにSNS情報を紐付け
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                    ]);
                }
            }
        }

        if (!$user) {
            // メールが null の場合（LINE 等）はプレースホルダーを使用（users.email は NOT NULL のため）
            $email = $socialUser->getEmail();
            if (empty($email)) {
                $email = $provider . '_' . $socialUser->getId() . '@sns.5akeme.local';
            }

            // 新規ユーザー作成
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'ユーザー',
                'email' => $email,
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => null, // SNSログインのみなのでパスワードなし
                'email_verified_at' => now(), // SNS経由なのでメール確認済みとする
            ]);
        }

        // ログイン
        Auth::login($user, true);

        return redirect()->intended(route('mypage'));
    }
}
