<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiagnoseController;
use App\Http\Controllers\RecommendController;
use App\Http\Controllers\StoreSuggestionController;

/**
 * 疎通確認
 * GET /api/ping
 */
Route::get('/ping', fn () => response()->json([
    'pong' => now()->toISOString(),
]));

/**
 * 診断 API
 *  - POST /api/diagnose/start  … 質問セット生成（5問）
 *  - POST /api/diagnose/score  … 採点して result_id を返す
 */
Route::prefix('diagnose')->group(function () {
    Route::post('/start', [DiagnoseController::class, 'start'])
        ->name('diagnose.start');

    Route::post('/score', [DiagnoseController::class, 'score'])
        ->name('diagnose.score');
});

/**
 * 店舗レコメンド系（今回はおまけ）
 */
Route::get('/recommend/stores', [RecommendController::class, 'index'])
    ->name('recommend.stores');

Route::get('/stores/suggest', [StoreSuggestionController::class, 'suggest'])
    ->name('stores.suggest');
// トップページ
Route::get('/', function () {
    return view('top');
})->name('top');

// 診断ページ（中身はこれから育てる用のプレースホルダ）
Route::get('/diagnose', function () {
    return view('diagnose');
})->name('diagnose.show');