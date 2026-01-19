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
        Schema::create('store_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');  // 店舗ID
            $table->json('update_types')->nullable();                            // 報告種別（複数選択）
            $table->text('detail');                                              // 詳細内容
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');  // 対応状況
            $table->text('admin_note')->nullable();                              // 管理者メモ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_reports');
    }
};
