<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use App\Jobs\PingJob;
use App\Http\Controllers\DiagnoseController;
use App\Http\Controllers\TopController;
use App\Http\Controllers\AgeCheckController;

/*
|--------------------------------------------------------------------------
| Entrance（入口）
|--------------------------------------------------------------------------
| WELCOME画面
| 役割：世界観提示・入店トリガーのみ
*/
Route::view('/', 'welcome')->name('welcome');


/*
|--------------------------------------------------------------------------
| Age Check（年齢確認）
|--------------------------------------------------------------------------
| 法務要件・Cookie管理はすべてここに集約
*/
Route::get('/age-check', [AgeCheckController::class, 'show'])
    ->name('age.check');

Route::post('/age-check/verify', [AgeCheckController::class, 'verify'])
    ->name('age.verify');

Route::get('/age-denied', [AgeCheckController::class, 'denied'])
    ->name('age.denied');


/*
|--------------------------------------------------------------------------
| Inside Store（店内）
|--------------------------------------------------------------------------
| 年確済みユーザーのみアクセス可能
*/
Route::get('/top', [TopController::class, 'show'])
    ->middleware('age.verified')
    ->name('top');


/*
|--------------------------------------------------------------------------
| Diagnose（診断）
|--------------------------------------------------------------------------
*/
Route::view('/diagnose', 'diagnose')->name('diagnose');

Route::get('/diagnose/result/{id}', [DiagnoseController::class, 'showResult'])
    ->name('diagnose.result');


/*
|--------------------------------------------------------------------------
| Health Check / Ops（運用・監視）
|--------------------------------------------------------------------------
*/
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

        return response()->json([
            'redis' => $redisOk ? 'ok' : 'ng'
        ]);
    });
});


/*
|--------------------------------------------------------------------------
| Debug（非本番のみ）
|--------------------------------------------------------------------------
*/
if (!app()->isProduction()) {
    Route::get('/debug/queue', function () {
        PingJob::dispatch('hello-from-queue');
        return 'queued';
    });

    Route::get('/debug/db-config', function () {
        $dbConfig = config('database.connections.' . config('database.default'));
        return response()->json([
            'default_connection' => config('database.default'),
            'host' => $dbConfig['host'] ?? 'not set',
            'port' => $dbConfig['port'] ?? 'not set',
            'database' => $dbConfig['database'] ?? 'not set',
            'username' => $dbConfig['username'] ?? 'not set',
            'env_db_host' => env('DB_HOST', 'not set'),
            'env_db_connection' => env('DB_CONNECTION', 'not set'),
        ]);
    });

    Route::get('/debug/db-test', function () {
        try {
            \DB::connection()->getPdo();
            return response()->json([
                'status' => 'success',
                'message' => 'Database connection successful',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'host' => config('database.connections.' . config('database.default') . '.host'),
            ], 500);
        }
    });
}

// 一番最初の入口（必ずWELCOME）
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
