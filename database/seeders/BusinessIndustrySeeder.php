<?php

namespace Database\Seeders;

use App\Models\BusinessIndustries;
use Illuminate\Database\Seeder;

class BusinessIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BusinessIndustries::insert(
            [
                [
                    'name' => 'IT',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => 'IT',
                ],
                [
                    'name' => 'コンサルティング',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => 'コンサルティング',
                ],
                [
                    'name' => 'メディア/広告/出版',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => 'メディア',
                ],
                [
                    'name' => '金融/保険',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '金融',
                ],
                [
                    'name' => '金融/保険',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '金融',
                ],
                [
                    'name' => '小売/EC',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '小売',
                ],
                [
                    'name' => 'エンタメ（旅行/スポーツ等）',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => 'エンタメ（旅行',
                ],
                [
                    'name' => '教育/人材',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '教育',
                ],
                [
                    'name' => 'アパレル/ファッション',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => 'アパレル',
                ],
                [
                    'name' => '医療/福祉',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '医療',
                ],

                [
                    'name' => '飲食/フード',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '飲食',
                ],

                [
                    'name' => '商社',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '商社',
                ],

                [
                    'name' => '運輸/物流/自動車',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '運輸',
                ],

                [
                    'name' => '不動産',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '不動産',
                ],

                [
                    'name' => '士業（会計士/弁護士等）',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '士業（会計士',
                ],

                [
                    'name' => '官公庁/NPO/NGO',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => '官公庁',
                ],

                [
                    'name' => 'その他',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'slug' => 'その他',
                ],
            ]
        );
    }
}
