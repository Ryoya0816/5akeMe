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
        Schema::table('diagnose_results', function (Blueprint $table) {
            // 回答パターンのスナップショット（学習用）
            $table->json('answers_snapshot')->nullable()->after('top5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnose_results', function (Blueprint $table) {
            $table->dropColumn('answers_snapshot');
        });
    }
};
