<?php

namespace Database\Seeders;

use App\Http\Actions\GetInternalId;
use App\Models\Students;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $student = new Students();
        $student->family_name = 'Faker';
        $student->first_name = 'Test';
        $student->family_name_furigana = 'Faker';
        $student->first_name_furigana = 'Test';
        $student->email_valid = 'test@student.com';
        $student->email_invalid = '';
        $student->is_email_approved = 1;
        $student->education_facility_id = 5;
        $student->university_name = 'Faker';
        $student->graduate_year = 2022;
        $student->password = Hash::make('12345678');
        $student->graduate_month = 02;
        $student->self_introduction = 'Faker';
        $student->status = 1;
        $student->student_internal_id = GetInternalId::get_internal_student_id(rand(10, 1000), '2022-03-30');
        $student->save();

    }
}
