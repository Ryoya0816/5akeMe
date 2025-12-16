<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnoseResult extends Model
{
    protected $table = 'diagnose_results';

    protected $fillable = [
        'result_id',
        'primary_type',
        'primary_label',
        'mood',
        'candidates',
        // 'raw_scores',
    ];

    protected $casts = [
        'candidates' => 'array',
        // 'raw_scores' => 'array',
    ];
}