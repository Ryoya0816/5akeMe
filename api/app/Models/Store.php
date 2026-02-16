<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'business_hours',
        'closed_days',
        'sake_types',
        'mood',
        'website_url',
        'is_active',
    ];

    protected $casts = [
        'sake_types' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * 雰囲気タグの選択肢
     */
    public static function moodOptions(): array
    {
        return [
            'lively' => 'にぎやか系（わいわい/サクッと/ガッツリ）',
            'calm' => '落ち着き系（しっとり/一人で静かに）',
            'both' => 'どちらもOK',
        ];
    }

    /**
     * 指定した雰囲気の店舗を取得
     */
    public function scopeWithMood($query, string $mood)
    {
        return $query->where(function ($q) use ($mood) {
            $q->where('mood', $mood)
              ->orWhere('mood', 'both');
        });
    }

    /**
     * 営業時間・開店の選択肢（30分刻み 00:00〜24:00 + 不定）
     */
    public static function businessHoursOpenOptions(): array
    {
        $options = ['不定' => '不定'];
        for ($h = 0; $h < 24; $h++) {
            $options[sprintf('%02d:00', $h)] = sprintf('%02d:00', $h);
            $options[sprintf('%02d:30', $h)] = sprintf('%02d:30', $h);
        }
        $options['24:00'] = '24:00（終日）';
        return $options;
    }

    /**
     * 営業時間・閉店の選択肢（30分刻み 00:00〜24:00、翌1:00〜翌6:00 + 不定）
     */
    public static function businessHoursCloseOptions(): array
    {
        $options = ['不定' => '不定'];
        for ($h = 0; $h < 24; $h++) {
            $options[sprintf('%02d:00', $h)] = sprintf('%02d:00', $h);
            $options[sprintf('%02d:30', $h)] = sprintf('%02d:30', $h);
        }
        $options['24:00'] = '24:00';
        for ($h = 1; $h <= 6; $h++) {
            $options['翌' . $h . ':00'] = '翌' . $h . ':00';
            $options['翌' . $h . ':30'] = '翌' . $h . ':30';
        }
        return $options;
    }

    /**
     * 営業時間を開店・閉店にパース（"17:00〜24:00" → ['17:00', '24:00']）
     */
    public static function parseBusinessHours(?string $value): array
    {
        if (!$value) {
            return ['', ''];
        }
        if ($value === '不定' || !str_contains($value, '〜')) {
            return $value === '不定' ? ['不定', '不定'] : ['', ''];
        }
        $parts = explode('〜', $value, 2);
        return [
            trim($parts[0] ?? ''),
            trim($parts[1] ?? ''),
        ];
    }

    /**
     * 開店・閉店を営業時間文字列に結合
     */
    public static function combineBusinessHours(?string $open, ?string $close): ?string
    {
        if (!$open || !$close) {
            return null;
        }
        if ($open === '不定' || $close === '不定') {
            return '不定';
        }
        return $open . '〜' . $close;
    }

    /**
     * 定休日の選択肢（プルダウン用）
     */
    public static function closedDaysOptions(): array
    {
        return [
            '月曜' => '月曜',
            '火曜' => '火曜',
            '水曜' => '水曜',
            '木曜' => '木曜',
            '金曜' => '金曜',
            '土曜' => '土曜',
            '日曜' => '日曜',
            '月曜・火曜' => '月曜・火曜',
            '水曜・木曜' => '水曜・木曜',
            '日曜・祝日' => '日曜・祝日',
            '不定休' => '不定休',
            '無休' => '無休',
        ];
    }

    /**
     * お酒タイプの選択肢（チェック方式・管理画面用）
     */
    public static function sakeTypeOptions(): array
    {
        return [
            'sake_dry' => '日本酒（辛口）',
            'sake_sweet' => '日本酒（甘口）',
            'shochu_mugi' => '焼酎（麦）',
            'shochu_imo' => '焼酎（芋）',
            'shochu_kome' => '焼酎（米）',
            'beer_draft' => '生ビール',
            'craft_beer' => 'クラフトビール',
            'wine_red' => 'ワイン（赤）',
            'wine_white' => 'ワイン（白）',
            'wine_sparkling' => 'ワイン（スパークリング）',
            'whisky' => 'ウイスキー',
            'non_alcohol' => 'ノンアル豊富',
            'cocktail' => 'カクテル豊富',
            'other' => 'その他',
            'saga_local_sake' => '佐賀の地酒',
        ];
    }

    /** 診断結果には表示しないタグ（店舗詳細のみ表示） */
    public static function getDisplayOnlyTags(): array
    {
        return ['saga_local_sake'];
    }

    /**
     * お酒タイプのラベル取得（表示用・旧データのフォールバック付き）
     */
    public static function getSakeTypeLabel(string $type): string
    {
        $labels = [
            ...self::sakeTypeOptions(),
            'sake_sparkling' => '日本酒（スパークリング）',
            'beer_lager' => 'ビール（ラガー）',
            'beer_ale' => 'ビール（エール）',
            'highball' => 'ハイボール',
            'umeshu' => '梅酒',
            'saga_local_sake' => '佐賀の地酒',
        ];
        return $labels[$type] ?? $type;
    }

    /**
     * 指定したお酒タイプを持つ店舗を取得
     */
    public function scopeWithSakeType($query, string $type)
    {
        return $query->whereJsonContains('sake_types', $type);
    }

    /**
     * 有効な店舗のみ取得
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
