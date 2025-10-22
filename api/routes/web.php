<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use App\Jobs\PingJob;

Route::get('/debug/queue', function () {
    PingJob::dispatch('hello-from-queue');
    return 'queued';
});


Route::get('/health/db', function () {
    try { DB::select('select 1'); $dbOk = true; } catch (\Throwable $e) { $dbOk = false; }
    return view('health', ['dbOk' => $dbOk]);
});

Route::get('/health/redis', function () {
    try { Redis::ping(); $redisOk = true; } catch (\Throwable $e) { $redisOk = false; }
    return response()->json(['redis' => $redisOk ? 'ok' : 'ng']);
});

if (!app()->isProduction()) {
    Route::get('/debug/queue', function () {
        \App\Jobs\PingJob::dispatch('hello-from-queue');
        return 'queued';
    });
}
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/health/db', /* 既存クロージャ */);
    Route::get('/health/redis', /* 既存クロージャ */);
});
Route::get('/diagnose', function () {
    return view('diagnose'); // resources/views/diagnose.blade.php
});