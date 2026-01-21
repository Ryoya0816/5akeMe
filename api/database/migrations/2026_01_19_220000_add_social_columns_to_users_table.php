<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        
        // パスワードをnullable（SNSログインのみのユーザー用）- MySQL直接
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NULL');
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
