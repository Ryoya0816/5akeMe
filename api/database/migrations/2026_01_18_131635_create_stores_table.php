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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();                                          // 店ID（自動採番）
            $table->string('name');                                // 店名
            $table->string('address')->nullable();                 // 住所
            $table->string('phone')->nullable();                   // 電話番号
            $table->string('business_hours')->nullable();          // 営業時間
            $table->string('closed_days')->nullable();             // 定休日
            $table->json('sake_types')->nullable();                // おすすめの酒タイプ（複数可）
            $table->string('website_url')->nullable();             // お店のHP等リンク
            $table->boolean('is_active')->default(true);           // 表示/非表示フラグ
            $table->timestamps();                                  // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
