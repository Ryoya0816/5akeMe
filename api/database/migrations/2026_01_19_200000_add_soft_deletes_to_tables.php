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
        // stores テーブル
        Schema::table('stores', function (Blueprint $table) {
            $table->softDeletes();
        });

        // store_reports テーブル
        Schema::table('store_reports', function (Blueprint $table) {
            $table->softDeletes();
        });

        // diagnose_feedbacks テーブル
        Schema::table('diagnose_feedbacks', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('store_reports', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('diagnose_feedbacks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
