<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiagnoseController;
use App\Http\Controllers\RecommendController;

/**
 * 疎通テスト
 */
Route::get('/ping', fn () => response()->json(['pong' => now()->toISOString()]));

/**
 * 診断フロー
 * - /diagnose/session : 出題（固定2＋A/B/C各1、seedで再現性あり）
 * - /diagnose/answer  : 回答集計（Q2×1.5、3点幅、同点fallback、mood返却）
 */
Route::post('/diagnose/session', [DiagnoseController::class, 'createSession']);
Route::post('/diagnose/answer',  [DiagnoseController::class, 'answer']);

/**
 * 店舗レコメンド（実装済みなら）
 * 例: GET /recommend/stores?sake_type=SA-K&mood=2&limit=3
 */
Route::get('/recommend/stores', [RecommendController::class, 'index']);
