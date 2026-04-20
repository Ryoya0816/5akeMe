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

        return redirect()
            ->route('top')
            ->withCookie(cookie('age_verified', '1', $minutes));
    }

    public function denied()
    {
        return view('age_denied');
    }
}
