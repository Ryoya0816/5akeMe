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
        Schema::table('users', function (Blueprint $table) {
            // SNS連携用カラム
            $table->string('provider')->nullable()->after('password');        // google, line, twitter
            $table->string('provider_id')->nullable()->after('provider');     // SNSのユーザーID
            $table->string('avatar')->nullable()->after('provider_id');       // プロフィール画像URL

            // インデックス
            $table->index(['provider', 'provider_id']);
        });

        // パスワードを nullable（SNSのみユーザー用）— MySQL / SQLite 共通（doctrine/dbal）
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['provider', 'provider_id']);
            $table->dropColumn(['provider', 'provider_id', 'avatar']);
        });
    }
};
