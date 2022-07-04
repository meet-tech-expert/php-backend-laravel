<?php

namespace Database\Seeders;

use App\Models\WorkCategories;
use Illuminate\Database\Seeder;

class WorkCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WorkCategories::insert([
            [
                'slug' => 'エンジニア',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => 'エンジニア',
            ],
            [
                'slug' => 'デザイナー',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => 'デザイナー',
            ],
            [
                'slug' => '営業',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => '営業',
            ],
            [
                'slug' => '企画',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => '企画',
            ],
            [
                'slug' => 'マーケティング',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => 'マーケティング',
            ],
            [
                'slug' => '編集',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => '編集/ライター',
            ],
            [
                'slug' => 'コーポレートスタッフ',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => 'コーポレートスタッフ',
            ],
            [
                'slug' => 'アンバサダー',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => 'アンバサダー',
            ],
            [
                'slug' => 'その他',
                'created_at' => now(),
                'updated_at' => now(),
                'name' => 'その他',
            ],
        ]);
    }
}
