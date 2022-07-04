<?php

namespace Database\Seeders;

use App\Models\InternshipFeatures;
use Illuminate\Database\Seeder;

class InternshipFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InternshipFeatures::insert([
            [
                'name' => 'CXO直下',
                'slug' => 'CXO直下',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '週1~2',
                'slug' => '週1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '週3~4',
                'slug' => '週3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'メンター在籍',
                'slug' => 'メンター在籍',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '未経験OK',
                'slug' => '未経験OK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '好きな時勤務',
                'slug' => '好きな時勤務',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '女性が活躍',
                'slug' => '女性が活躍',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '英語を使う',
                'slug' => '英語を使う',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '短期・単発',
                'slug' => '短期・単発',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '短時間OK',
                'slug' => '短時間OK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '土日祝休み',
                'slug' => '土日祝休み',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '残業なし',
                'slug' => '残業なし',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '服装自由',
                'slug' => '服装自由',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '大量募集',
                'slug' => '大量募集',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '人気',
                'slug' => '人気',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '高時給',
                'slug' => '高時給',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '新規事業立ち上げ',
                'slug' => '新規事業立ち上げ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '外資系',
                'slug' => '外資系',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '内定実績多数',
                'slug' => '内定実績多数',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'インセンティブ/成果報酬あり',
                'slug' => 'インセンティブ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '留学生歓迎',
                'slug' => '留学生歓迎',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '理系学生におすすめ',
                'slug' => '理系学生におすすめ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ベンチャー',
                'slug' => 'ベンチャー',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'エリート社員',
                'slug' => 'エリート社員',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '起業ノウハウが身に付く',
                'slug' => '起業ノウハウが身に付く',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '土日だけでもOK',
                'slug' => '土日だけでもOK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '積極採用中',
                'slug' => '積極採用中',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '他社内定者OK',
                'slug' => '他社内定者OK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
