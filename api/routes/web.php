<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use App\Jobs\PingJob;
use App\Http\Controllers\DiagnoseController;

// ---- トップページ ----
// / に来たら「年齢確認 → OKなら top.blade.php」を表示
Route::get('/', function () {
    // Cookie が無ければ年齢確認ページへ
    if (!request()->cookie('age_verified')) {
        return redirect()->route('age.check');
    }

    // Cookie があれば top.blade.php を表示
    return view('top');
})->name('top');


// ---- デバッグ用 ----
if (!app()->isProduction()) {
    Route::get('/debug/queue', fn() => (PingJob::dispatch('hello-from-queue')) && 'queued');
}

// ---- ヘルスチェック ----
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/health/db', function () {
        try {
            DB::select('select 1');
            $dbOk = true;
        } catch (\Throwable $e) {
            $dbOk = false;
        }

        return view('health', ['dbOk' => $dbOk]);
    });

    Route::get('/health/redis', function () {
        try {
            Redis::ping();
            $redisOk = true;
        } catch (\Throwable $e) {
            $redisOk = false;
        }

        return response()->json(['redis' => $redisOk ? 'ok' : 'ng']);
    });
});

// ---- 診断系 ----

// /diagnose 画面（チャットUI）
// resources/views/diagnose.blade.php を表示
Route::view('/diagnose', 'diagnose')->name('diagnose');

// 結果ページ（Web側で表示）
Route::get('/diagnose/result/{id}', [DiagnoseController::class, 'showResult'])
    ->name('diagnose.result');

// 年齢確認画面
Route::get('/age-check', function () {
    return view('age_check');
})->name('age.check');

// 「はい」→ Cookie 保存してTOPへ
Route::post('/age-check/verify', function () {
    return redirect()->route('top')
        ->withCookie(cookie()->forever('age_verified', true));
})->name('age.verify');

// 「いいえ」→ メッセージ表示ページへ
Route::get('/age-denied', function () {
    return view('age_denied'); 
})->name('age.denied');

