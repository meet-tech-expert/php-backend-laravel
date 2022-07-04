<?php

namespace App\Http\Actions;

use App\Models\Company;
use App\Models\Students;
use Illuminate\Support\Facades\Log;


class GetInternalId {


    public static function get_internal_application_id ($student_id, $application_id) {
        return
        'a' .
        str_replace('s', '', Students::findOrFail($student_id)->student_internal_id).
        '-' .
        $application_id;
    }

    public static function get_internal_student_id ($student_id) {

        $id = 'S' . date('y') . str_pad($student_id, 6, '0', STR_PAD_LEFT);
        
        // if (Students::where('student_internal_id', $id)->exists()) {
        //     return self::get_internal_student_id($student_id);
        // }

        // if (date('y') < date('y', strtotime(Students::orderBy('created_at', 'desc')->first()->created_at))) {
        //     return self::get_internal_student_id(1);
        // }


        return $id;
    }

    public static function get_internal_internship_post_id ($internship_post_id, $company_id) {
        return
        'i' .
        str_replace('C', '', Company::findOrFail($company_id)->internal_company_id).
        '-' .
        $internship_post_id;
    }

    public static function get_internal_company_id ($company_id) {

        $id = 'B' . date('y') . str_pad($company_id, 6, '0', STR_PAD_LEFT);

        if (Company::where('internal_company_id', $id)->exists()) {
            return self::get_internal_company_id($company_id);
        }

        if (date('y') < date('y', strtotime(Company::orderBy('created_at', 'desc')->first()->created_at))) {
            return self::get_internal_company_id(1);
        }
        return $id;
    }
}
