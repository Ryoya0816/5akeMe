<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

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
     * お酒タイプの選択肢
     */
    public static function sakeTypeOptions(): array
    {
        return [
            'sake_dry' => '日本酒（辛口）',
            'sake_sweet' => '日本酒（甘口）',
            'sake_sparkling' => '日本酒（スパークリング）',
            'shochu_mugi' => '焼酎（麦）',
            'shochu_imo' => '焼酎（芋）',
            'shochu_kome' => '焼酎（米）',
            'wine_red' => 'ワイン（赤）',
            'wine_white' => 'ワイン（白）',
            'wine_sparkling' => 'ワイン（スパークリング）',
            'beer_lager' => 'ビール（ラガー）',
            'beer_ale' => 'ビール（エール）',
            'beer_craft' => 'クラフトビール',
            'whisky' => 'ウイスキー',
            'highball' => 'ハイボール',
            'cocktail' => 'カクテル',
            'umeshu' => '梅酒',
            'other' => 'その他',
        ];
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
