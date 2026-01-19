<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiagnoseResult extends Model
{
    protected $table = 'diagnose_results';

    protected $fillable = [
        'result_id',
        'primary_type',
        'primary_label',
        'mood',
        'candidates',
        'top5',
        'answers_snapshot',  // 回答パターン（学習用）
    ];

    protected $casts = [
        'candidates' => 'array',
        'top5' => 'array',
        'answers_snapshot' => 'array',
    ];

    /**
     * フィードバック（1対1）
     */
    public function feedback(): HasOne
    {
        return $this->hasOne(DiagnoseFeedback::class);
    }

    /**
     * フィードバック済みかどうか
     */
    public function hasFeedback(): bool
    {
        return $this->feedback()->exists();
    }
}