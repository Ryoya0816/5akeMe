<?php
// データベースにtop5カラムが存在するか確認するスクリプト

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    $columns = Schema::getColumnListing('diagnose_results');
    echo "diagnose_resultsテーブルのカラム一覧:\n";
    foreach ($columns as $column) {
        echo "  - $column\n";
    }
    
    if (in_array('top5', $columns)) {
        echo "\n✓ top5カラムは既に存在しています。\n";
    } else {
        echo "\n✗ top5カラムが存在しません。マイグレーションを実行してください。\n";
    }
} catch (\Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
