<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 診断結果へのフィードバック
 * - 1-5点の評価
 * - 学習用データとして回答パターンと結果をスナップショット保存
 */
class DiagnoseFeedback extends Model
{
    use HasFactory;

    /**
     * テーブル名を明示的に指定
     */
    protected $table = 'diagnose_feedbacks';

    protected $fillable = [
        'diagnose_result_id',
        'rating',
        'comment',
        'answers_snapshot',
        'result_type',
        'mood',
    ];

    protected $casts = [
        'answers_snapshot' => 'array',
        'rating' => 'integer',
    ];

    /**
     * 評価の選択肢（表示用）
     */
    public static function ratingOptions(): array
    {
        return [
            1 => '⭐ イマイチ',
            2 => '⭐⭐ まあまあ',
            3 => '⭐⭐⭐ 普通',
            4 => '⭐⭐⭐⭐ 良い',
            5 => '⭐⭐⭐⭐⭐ 最高！',
        ];
    }

    /**
     * 評価のラベルを取得
     */
    public function getRatingLabelAttribute(): string
    {
        return self::ratingOptions()[$this->rating] ?? '不明';
    }

    /**
     * 紐づく診断結果
     */
    public function diagnoseResult(): BelongsTo
    {
        return $this->belongsTo(DiagnoseResult::class);
    }

    /**
     * 高評価かどうか（4点以上）
     */
    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * 低評価かどうか（2点以下）
     */
    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }
}
