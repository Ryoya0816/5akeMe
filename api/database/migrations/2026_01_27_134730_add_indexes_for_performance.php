<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * パフォーマンス向上のためのインデックス追加
     */
    public function up(): void
    {
        // users テーブル
        Schema::table('users', function (Blueprint $table) {
            // is_active でのフィルタリング用
            $table->index('is_active');
            // is_admin でのフィルタリング用
            $table->index('is_admin');
            // 作成日でのソート用
            $table->index('created_at');
        });

        // stores テーブル（存在する場合）
        if (Schema::hasTable('stores')) {
            Schema::table('stores', function (Blueprint $table) {
                // mood でのフィルタリング用
                if (Schema::hasColumn('stores', 'mood')) {
                    $table->index('mood');
                }
                // is_active でのフィルタリング用
                if (Schema::hasColumn('stores', 'is_active')) {
                    $table->index('is_active');
                }
            });
        }

        // diagnose_results テーブル（存在する場合）
        if (Schema::hasTable('diagnose_results')) {
            Schema::table('diagnose_results', function (Blueprint $table) {
                // primary_type でのフィルタリング用
                if (Schema::hasColumn('diagnose_results', 'primary_type')) {
                    $table->index('primary_type');
                }
                // 作成日でのソート用
                $table->index('created_at');
            });
        }

        // store_reports テーブル（存在する場合）
        if (Schema::hasTable('store_reports')) {
            Schema::table('store_reports', function (Blueprint $table) {
                // status でのフィルタリング用
                if (Schema::hasColumn('store_reports', 'status')) {
                    $table->index('status');
                }
            });
        }

        // user_visited_stores 中間テーブル（存在する場合）
        if (Schema::hasTable('user_visited_stores')) {
            Schema::table('user_visited_stores', function (Blueprint $table) {
                // visited_at でのソート用
                if (Schema::hasColumn('user_visited_stores', 'visited_at')) {
                    $table->index('visited_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_admin']);
            $table->dropIndex(['created_at']);
        });

        if (Schema::hasTable('stores')) {
            Schema::table('stores', function (Blueprint $table) {
                if (Schema::hasColumn('stores', 'mood')) {
                    $table->dropIndex(['mood']);
                }
                if (Schema::hasColumn('stores', 'is_active')) {
                    $table->dropIndex(['is_active']);
                }
            });
        }

        if (Schema::hasTable('diagnose_results')) {
            Schema::table('diagnose_results', function (Blueprint $table) {
                if (Schema::hasColumn('diagnose_results', 'primary_type')) {
                    $table->dropIndex(['primary_type']);
                }
                $table->dropIndex(['created_at']);
            });
        }

        if (Schema::hasTable('store_reports')) {
            Schema::table('store_reports', function (Blueprint $table) {
                if (Schema::hasColumn('store_reports', 'status')) {
                    $table->dropIndex(['status']);
                }
            });
        }

        if (Schema::hasTable('user_visited_stores')) {
            Schema::table('user_visited_stores', function (Blueprint $table) {
                if (Schema::hasColumn('user_visited_stores', 'visited_at')) {
                    $table->dropIndex(['visited_at']);
                }
            });
        }
    }
};
