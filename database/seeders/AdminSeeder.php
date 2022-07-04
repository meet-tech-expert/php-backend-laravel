<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::forceCreate([

            'name' => 'Moto',
            'email' => 'business@motocle.com',
            'password' => Hash::make('amwTdv,PjU%$2V4?'),
            'status'   =>'1',
        ]);
    }
}
