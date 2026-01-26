<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * アカウントが停止されているユーザーをブロック
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // is_activeカラムが存在し、falseの場合はログアウト
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'このアカウントは停止されています。詳しくはお問い合わせください。',
                    ]);
            }
        }

        return $next($request);
    }
}
