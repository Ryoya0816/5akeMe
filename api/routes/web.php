<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use App\Jobs\PingJob;
use App\Http\Controllers\DiagnoseController;

// トップを /diagnose へ
Route::get('/', fn() => redirect('/diagnose'));

// デバッグ用
if (!app()->isProduction()) {
    Route::get('/debug/queue', fn() => (PingJob::dispatch('hello-from-queue')) && 'queued');
}

// ヘルスチェック
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/health/db', function () {
        try { DB::select('select 1'); $dbOk = true; } catch (\Throwable $e) { $dbOk = false; }
        return view('health', ['dbOk' => $dbOk]);
    });
    Route::get('/health/redis', function () {
        try { Redis::ping(); $redisOk = true; } catch (\Throwable $e) { $redisOk = false; }
        return response()->json(['redis' => $redisOk ? 'ok' : 'ng']);
    });
});

// ---- 診断系 ----
Route::view('/diagnose', 'diagnose')->name('diagnose');
// ↑ ここはあなたのBlade名に合わせて変更


    // /diagnose 画面（チャットUI）
Route::view('/diagnose', 'diagnose')->name('diagnose');

// 結果ページ（Web側で表示）
Route::get('/diagnose/result/{id}', [DiagnoseController::class, 'showResult'])
    ->name('diagnose.result');
