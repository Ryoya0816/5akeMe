<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiagnoseController;
use App\Http\Controllers\DiagnoseFeedbackController;
use App\Http\Controllers\RecommendController;
use App\Http\Controllers\StoreSuggestionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| ここは「JSONを返すAPI専用」。
| view() を返すルート（トップ/診断ページ等）は routes/web.php に置く。
|--------------------------------------------------------------------------
*/

/**
 * 疎通確認
 * GET /api/ping
 */
Route::get('/ping', fn () => response()->json([
    'pong' => now()->toISOString(),
]))->name('api.ping');

/**
 * 診断 API
 *  - POST /api/diagnose/start  … 質問セット生成（5問）
 *  - POST /api/diagnose/score  … 採点して result_id を返す
 * 
 * レート制限: 1分間に30リクエストまで
 */
Route::prefix('diagnose')
    ->name('api.diagnose.')
    ->middleware('throttle:30,1')
    ->group(function () {
        Route::post('/start', [DiagnoseController::class, 'start'])
            ->name('start');

        Route::post('/score', [DiagnoseController::class, 'score'])
            ->name('score');

        // フィードバック
        Route::post('/feedback/{resultId}', [DiagnoseFeedbackController::class, 'store'])
            ->name('feedback.store');

        Route::get('/feedback/{resultId}/check', [DiagnoseFeedbackController::class, 'check'])
            ->name('feedback.check');
    });

/**
 * 店舗レコメンド系（今回はおまけ）
 */
Route::get('/recommend/stores', [RecommendController::class, 'index'])
    ->middleware('throttle:30,1') // 1分間に30回まで
    ->name('api.recommend.stores');

Route::get('/stores/suggest', [StoreSuggestionController::class, 'suggest'])
    ->middleware('throttle:30,1') // 1分間に30回まで
    ->name('api.stores.suggest');
