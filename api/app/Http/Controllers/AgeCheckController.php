<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgeCheckController extends Controller
{
    public function show(Request $request)
    {
        // 年確済みならスキップして店内へ
        if ($request->cookie('age_verified') === '1') {
            return redirect()->route('top');
        }

        return view('age_check');
    }

    public function verify(Request $request)
    {
        // 1年（分）
        $minutes = 60 * 24 * 365;

        // セキュアなCookie設定
        // - httpOnly: JavaScriptからアクセス不可（XSS対策）
        // - secure: HTTPS のときだけ。APP_URL が http:// なら必ず false（リバースプロキシで secure() がずれる場合の保険）
        // - sameSite: CSRF対策
        $secure = $request->secure() && str_starts_with(config('app.url', ''), 'https://');
        
        $cookie = cookie(
            'age_verified',     // 名前
            '1',                // 値
            $minutes,           // 有効期限（分）
            '/',                // パス
            null,               // ドメイン（デフォルト）
            $secure,            // secure（本番のみ）
            true,               // httpOnly
            false,              // raw
            'Lax'               // sameSite
        );

        return redirect()
            ->route('top')
            ->withCookie($cookie);
    }

    public function denied()
    {
        return view('age_denied');
    }
}
