<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diagnose_feedbacks', function (Blueprint $table) {
            $table->id();
            
            // 診断結果への紐付け
            $table->foreignId('diagnose_result_id')
                  ->constrained('diagnose_results')
                  ->onDelete('cascade');
            
            // 評価（1-5点）
            $table->unsignedTinyInteger('rating');
            
            // 任意コメント
            $table->text('comment')->nullable();
            
            // 学習用：回答パターンのスナップショット（JSON）
            $table->json('answers_snapshot')->nullable();
            
            // 学習用：診断結果タイプ
            $table->string('result_type')->nullable();
            
            // 学習用：mood
            $table->string('mood')->nullable();
            
            $table->timestamps();
            
            // インデックス（分析用）
            $table->index('rating');
            $table->index('result_type');
            $table->index('mood');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnose_feedbacks');
    }
};
