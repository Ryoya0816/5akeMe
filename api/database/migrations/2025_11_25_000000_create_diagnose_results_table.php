<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnose_results', function (Blueprint $table) {
            $table->id();

            // フロントから参照するID（/diagnose/result/{result_id}）
            $table->string('result_id')->unique();

            // メインのタイプ（例：sake_dry）
            $table->string('primary_type');

            // 表示用ラベル（例：「日本酒・辛口」）
            $table->string('primary_label');

            // moodタグ（lively / chill / silent / light / strong）
            $table->string('mood')->nullable();

            // 候補一覧（type, score, label の配列をJSON保存）
            $table->json('candidates');

            // 必要ならスコアの生配列も持てる（あとで追加してもOK）
            // $table->json('raw_scores')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnose_results');
    }
};
