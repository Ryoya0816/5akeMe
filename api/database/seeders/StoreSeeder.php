<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * おすすめ店舗表示用のサンプル店舗を投入
     */
    public function run(): void
    {
        $stores = [
            [
                'name' => '佐賀駅前 日本酒バー さが',
                'address' => '佐賀県佐賀市駅前中央1-1-1',
                'phone' => '0952-00-0000',
                'business_hours' => '17:00〜24:00',
                'closed_days' => '日曜',
                'sake_types' => ['sake_dry', 'sake_sweet', 'wine_white', 'wine_red', 'wine_sparkling', 'saga_local_sake'],
                'mood' => 'both',
                'website_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'にぎやか居酒屋 わいわい亭',
                'address' => '佐賀県佐賀市駅前中央2-2-2',
                'phone' => '0952-00-0001',
                'business_hours' => '18:00〜翌2:00',
                'closed_days' => '不定休',
                'sake_types' => ['beer_draft', 'sake_dry', 'shochu_mugi'],
                'mood' => 'lively',
                'website_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'しっとり ワイン＆日本酒 落ち穂',
                'address' => '佐賀県佐賀市駅前中央3-3-3',
                'phone' => '0952-00-0002',
                'business_hours' => '18:00〜23:00',
                'closed_days' => '月曜',
                'sake_types' => ['wine_white', 'wine_red', 'wine_sparkling', 'sake_sweet', 'cocktail'],
                'mood' => 'calm',
                'website_url' => null,
                'is_active' => true,
            ],
        ];

        foreach ($stores as $row) {
            Store::updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
    }
}
