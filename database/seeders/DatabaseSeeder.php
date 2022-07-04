<?php

namespace Database\Seeders;

use App\Models\EducationFacilities;
use App\Models\MediaTags;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // MediaTags::factory(10)->create();
        // EducationFacilities::factory(10)->create();
        $this->call([
            AdminSeeder::class,
            BusinessIndustrySeeder::class,
            InternshipFeaturesSeeder::class,
            WorkCategorySeeder::class,
        ]);
    }
}
