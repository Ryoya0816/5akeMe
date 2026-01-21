<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'update_types',
        'detail',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'update_types' => 'array',
    ];

    /**
     * ステータスの選択肢
     */
    public static function statusOptions(): array
    {
        return [
            'pending'   => '📥 未対応',
            'reviewed'  => '👀 確認中',
            'resolved'  => '✅ 対応完了',
            'dismissed' => '❌ 却下',
        ];
    }

    /**
     * 報告種別の選択肢
     */
    public static function updateTypeOptions(): array
    {
        return [
            '営業時間' => '🕐 営業時間',
            '定休日'   => '📅 定休日',
            '電話番号' => '📞 電話番号',
            '住所'     => '📍 住所',
            '閉店'     => '🚫 閉店',
            'その他'   => '📝 その他',
        ];
    }

    /**
     * 紐づく店舗
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
