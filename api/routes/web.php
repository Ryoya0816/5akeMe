<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DiagnoseController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopController;
use App\Http\Controllers\AgeCheckController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use App\Jobs\PingJob;

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

Route::get('/diagnose/result/{resultId}', [DiagnoseController::class, 'showResult'])
    ->name('diagnose.result');


/*
|--------------------------------------------------------------------------
| Legal（法的ページ）
|--------------------------------------------------------------------------
*/
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');


/*
|--------------------------------------------------------------------------
| About / Contact（その他ページ）
|--------------------------------------------------------------------------
*/
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/contact/thanks', 'contact_thanks')->name('contact.thanks');


/*
|--------------------------------------------------------------------------
| Store（店舗）
|--------------------------------------------------------------------------
*/
Route::get('/store/{id}', function ($id) {
    $store = \App\Models\Store::findOrFail($id);
    return view('store_detail', ['store' => $store]);
})->name('store.detail');

Route::post('/store/{id}/report', [\App\Http\Controllers\StoreReportController::class, 'store'])
    ->name('store.report');


/*
|--------------------------------------------------------------------------
| Auth - SNS Login (Socialite)
|--------------------------------------------------------------------------
*/
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('auth.social.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('auth.social.callback');


/*
|--------------------------------------------------------------------------
| Auth - Breeze（メール/パスワード認証）
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| Mypage（マイページ）- 要ログイン
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Breezeのdashboardはmypageへリダイレクト
    Route::get('/dashboard', fn () => redirect()->route('mypage'))->name('dashboard');

    // マイページトップ
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');
    
    // 診断履歴
    Route::get('/mypage/history', [MypageController::class, 'history'])->name('mypage.history');
    
    // 行ったお店
    Route::get('/mypage/stores', [MypageController::class, 'stores'])->name('mypage.stores');
    Route::post('/mypage/stores/{store}', [MypageController::class, 'addStore'])->name('mypage.stores.add');
    Route::put('/mypage/stores/{store}', [MypageController::class, 'updateStore'])->name('mypage.stores.update');
    Route::delete('/mypage/stores/{store}', [MypageController::class, 'removeStore'])->name('mypage.stores.remove');
    
    // 傾向グラフ
    Route::get('/mypage/trend', [MypageController::class, 'trend'])->name('mypage.trend');
    
    // Breeze Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


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
}
