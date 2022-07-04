<?php

namespace App\Http\Controllers;

use App\Models\Applications;
use App\Models\BusinessIndustries;
use App\Models\Company;
use App\Models\InternshipFeatures;
use App\Models\InternshipPosts;
use App\Models\Students;
use App\Models\WorkCategories;

class MasterController extends Controller
{
    public function index()
    {
        $totalCompanies = Company::count();
        $totalCompaniesnotApproved = Company::where('status', '=', 0)->count();
        $workCategories = WorkCategories::orderByRaw('-display_order desc')->get();
        $businessIndustories = BusinessIndustries::all();
        $internPosts = InternshipPosts::where('status', 0)->where('draft_or_public', 1)->count();
        $total_students = Students::whereIn('status', [1, 2, 3])->whereNotNull('student_internal_id')->whereNotNull('email_valid')->count();
        $totalUnreadApplications = Applications::where('is_admin_read', '=', 0)->count();
        $newStudentArrival = Students::whereIn('status', [1, 2, 3])->whereNotNull('student_internal_id')->whereNotNull('email_valid')->where('is_admin_read', '=', 0)->count();

        $data = [
            'total_companies' => $totalCompanies,
            'total_companies_not_approved' => $totalCompaniesnotApproved,
            'work_categories' => $workCategories,
            'business_industories' => $businessIndustories,
            'internship_feature_list' => InternshipFeatures::orderByRaw('-display_order desc')->get(),
            'total_internships' => $internPosts,
            'total_draft_internships' => InternshipPosts::where('draft_or_public', 0)->count(),
            'period' => config('constants.period'),
            'wage' => config('constants.wage'),
            'workload' => config('constants.workload'),
            'target_grade' => config('constants.target_grade'),
            'educational_facility_type' => config('constants.educational_facility_type'),
            'reviews_option' => config('constants.reviews_option'),
            'application_status' => config('constants.application_status'),
            'cancel_reasons' => config('constants.cancel_reasons'),
            'internship_posts_count' => $internPosts,
            'total_students' => $total_students,
            'new_student_arrival' => $newStudentArrival,
            'total_unread_applications'  => $totalUnreadApplications,
            'withdrawl_reasons' => config('constants.withdrawl_reasons')
        ];
        return $this->sendResponse($data);
    }
}
