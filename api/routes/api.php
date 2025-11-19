<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiagnoseController;
use App\Http\Controllers\RecommendController;
use App\Http\Controllers\StoreSuggestionController;

/** 疎通 */
Route::get('/ping', fn () => response()->json(['pong' => now()->toISOString()]));

/** 診断API */
Route::prefix('diagnose')->middleware('api')->group(function () {
    Route::get('/questions', [DiagnoseController::class, 'questions'])->name('diagnose.questions');
    Route::post('/session',   [DiagnoseController::class, 'session'])->name('diagnose.session');
    Route::post('/score',     [DiagnoseController::class, 'score'])->name('diagnose.score');
});

/** 店舗レコメンド */
Route::get('/recommend/stores', [RecommendController::class, 'index'])->name('recommend.stores');

Route::get('/stores/suggest', [StoreSuggestionController::class, 'suggest'])->name('stores.suggest');

// スコア計算API（POST専用）
Route::post('/diagnose/score', [DiagnoseController::class, 'score'])
    ->name('diagnose.score');