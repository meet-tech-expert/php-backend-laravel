<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\ApplicationsResource;
use App\Models\Applications;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationsRequest;
use App\Http\Actions\GetInternalId;
use App\Http\Resources\PaginationResource;
use App\Mail\DeclineInternshipMail;
use Illuminate\Support\Facades\Mail;

class ApplicationsController extends  Controller
{
    /**
     * Application List.
     * @group Application
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.show_all_success","data":[{"id":1,"student_id":1,"company_id":1,"internship_post_id":1,"status":null,"created_at":"2022-03-25T09:23:22.000000Z","updated_at":"2022-03-25T09:23:22.000000Z","deleted_at":null,"student":{"id":1,"family_name":"minhaz","first_name":"Ahamed","family_name_furigana":"","first_name_furigana":"","email_valid":"demo@gmail.com","email_invalid":null,"is_email_approved":0,"education_facility_id":1,"graduate_year":null,"graduate_month":null,"self_introduction":null,"status":1,"created_at":null,"updated_at":null,"deleted_at":null},"company":{"id":1,"internal_company_id":"demo","name":"www","furigana_name":"www","logo_img":null,"business_industry_id":null,"office_address":null,"office_phone":null,"office_email1":null,"status":1,"office_email2":null,"office_email3":null,"website_url":null,"client_liason":null,"admin_memo":null,"created_at":null,"updated_at":null,"deleted_at":null},"internship_post":{"id":1,"title":"sdfsdf","company_id":0,"business_industry_id":0,"work_category_id":0,"period":0,"workload":0,"internship_feature_id":0,"application_step_1":null,"application_step_2":null,"application_step_3":null,"application_step_4":null,"seo_slug":null,"seo_ogp":null,"seo_meta_description":null,"seo_featured_image":null,"description_corporate_profile":null,"description_internship_content":null,"draft_or_public":null,"page_views":null,"status":null,"created_at":null,"updated_at":null,"deleted_at":null,"wage":0,"target_grade":0,"public_date":null,"display_order":null}}]}}
     */

    public function index(Request $request)
    {
        $application = Applications::with('company', 'internshipPost.company','student.educationFacility')
                        ->whereHas('student', function($query) {
                            $query->whereRaw("RIGHT(students.email_valid, 10) != '_cancelled'");
                        })
                        ->where('status', $request->input('status',1))
                        ->orderBy($request->input('sort_by'), $request->input('sort_by_order','desc'));

		 $application->when($request->input('search'), function ($query, $search) {
                $query->where('id', 'LIKE', "%$search%");
				$query->orWhereHas('company', function($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                    $query->orWhere('internal_company_id', 'LIKE', "%$search%");
                });
				$query->orWhereHas('student', function($query) use ($search) {
                    $query->where('student_internal_id', 'LIKE', "%$search%");
                    $query->orWhere('family_name', 'LIKE', "%$search%");
                    $query->orWhere('first_name', 'LIKE', "%$search%");
                    $query->orWhere('email_valid', 'LIKE', "%$search%");
                });
            });
		$application->when($request->input('date_from'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->date_from);
                $query->where('created_at', '<=', $request->date_to);
         });
        $application = $request->input('paginate')
        ? $application->paginate($request->input('paginate', 25))
        : $application->get();

        try {

            return $this->sendResponse([
                'message' 	=> __('messages.show_all_success'),
                'data' 		=> ApplicationsResource::collection($application),
                'paginate' 	=> $request->input('paginate') ?  new PaginationResource($application): '',
				'counts' 	=> [
                    [
					'total_applied' => Applications::select('*')->where('status', 1)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 1)->where('is_admin_read', 0)->count(),
					],
					[
					'total_passed' => Applications::select('*')->where('status', 2)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 2)->where('is_admin_read', 0)->count(),
					],
                    [
					'total_completed' => Applications::select('*')->where('status', 3)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 3)->where('is_admin_read', 0)->count(),
					],
					[
					'total_failed' => Applications::select('*')->where('status', 4)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 4)->where('is_admin_read', 0)->count(),
					],
                    [
					'total_declined' => Applications::select('*')->where('status', 5)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 5)->where('is_admin_read', 0)->count(),
					],
                ],
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function create()
    {
        $application = new Applications();
        return $application;
    }


    /**
     * Application Create.
     * @group Application
     * @param Request $request
     * @bodyParam student_id integer Example: 1
     * @bodyParam company_id integer Example: 1
     * @bodyParam internship_post_id integer Example:1
     * @bodyParam status integer required Example: 1 .1 .application status 1: 応募済   applied2: 合格済   qualified3: 完了  done　4: 不合格  not-qualified 5: 辞退済   declined
     * @bodyParam is_admin_read integer  0 for unread and 1 for read Example:
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.record_created_successfully","data":{"student_id":"1","company_id":"1","internship_post_id":"1","updated_at":"2022-03-25T09:23:22.000000Z","created_at":"2022-03-25T09:23:22.000000Z","id":1}}}
     */

    public function store(ApplicationsRequest $request)
    {

        try {

            $requestedData = $request->validated();
            $Application = new Applications();
            $Application->student_id = $requestedData['student_id'];
            $Application->company_id = $requestedData['company_id'];
            $Application->internship_post_id = $requestedData['internship_post_id'];
            $Application->is_admin_read = $requestedData['is_admin_read'];
            $Application->internal_application_id = random_int(1, 99999);
            $Application->save();
            $Application->internal_application_id = GetInternalId::get_internal_application_id($requestedData['student_id'], $Application->id);
            $Application->save();

            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
                'data' => new ApplicationsResource($Application),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Application View.
     * @group Application
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.show_all_success","data":[{"id":1,"student_id":1,"company_id":1,"internship_post_id":1,"status":null,"created_at":"2022-03-25T09:23:22.000000Z","updated_at":"2022-03-25T09:23:22.000000Z","deleted_at":null,"student":{"id":1,"family_name":"minhaz","first_name":"Ahamed","family_name_furigana":"","first_name_furigana":"","email_valid":"demo@gmail.com","email_invalid":null,"is_email_approved":0,"education_facility_id":1,"graduate_year":null,"graduate_month":null,"self_introduction":null,"status":1,"created_at":null,"updated_at":null,"deleted_at":null},"company":{"id":1,"internal_company_id":"demo","name":"www","furigana_name":"www","logo_img":null,"business_industry_id":null,"office_address":null,"office_phone":null,"office_email1":null,"status":1,"office_email2":null,"office_email3":null,"website_url":null,"client_liason":null,"admin_memo":null,"created_at":null,"updated_at":null,"deleted_at":null},"internship_post":{"id":1,"title":"sdfsdf","company_id":0,"business_industry_id":0,"work_category_id":0,"period":0,"workload":0,"internship_feature_id":0,"application_step_1":null,"application_step_2":null,"application_step_3":null,"application_step_4":null,"seo_slug":null,"seo_ogp":null,"seo_meta_description":null,"seo_featured_image":null,"description_corporate_profile":null,"description_internship_content":null,"draft_or_public":null,"page_views":null,"status":null,"created_at":null,"updated_at":null,"deleted_at":null,"wage":0,"target_grade":0,"public_date":null,"display_order":null}}]}}
     * */
    public function show(Request $request, $id)
    {
        try {

            $application = Applications::with('student', 'company', 'internshipPost')->where('student_id', $id)->get();

            if ($application) {
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => new ApplicationsResource($application),
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function studentApplications($id) {
        try {

            $application = Applications::withTrashed()->with(['student', 'company', 'internshipPost' => fn($q) => $q->withTrashed()])->where('student_id', $id)->get();

            if ($application) {
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => $application,
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
 
    /**
     * Application Updatye.
     * @group Application
     * @param Request $request
     * @bodyParam student_id integer Example: 1
     * @bodyParam company_id integer Example: 1
     * @bodyParam internship_post_id integer Example: 1
     * @bodyParam status integer required Example: 1 .1 .application status 1: 応募済   applied2: 合格済   qualified3: 完了  done　4: 不合格  not-qualified 5: 辞退済   declined
     * @bodyParam is_admin_read integer  0 for unread and 1 for read Example:
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.update_success","data":{"student_id":"1","company_id":"1","internship_post_id":"1","updated_at":"2022-03-25T09:23:22.000000Z","created_at":"2022-03-25T09:23:22.000000Z","id":1}}}
     */
    public function update(ApplicationsRequest $request, $id)
    {
        try {
            $requestedData = $request->validated();

            $Application = Applications::findOrFail($id);

            $Application->student_id = $requestedData['student_id'];
            $Application->company_id = $requestedData['company_id'];
            $Application->internship_post_id = $requestedData['internship_post_id'];
            $Application->internship_post_id = $requestedData['internship_post_id'];
            $Application->status = $requestedData['status'];
            $Application->is_admin_read = $requestedData['is_admin_read'];
            $Application->cancel_reason = $requestedData['cancel_reason'];
            $Application->save();
            if($Application->status == 5) {
                $Application->load('company', 'student', 'internshipPost');
                if($Application->company->office_email1) {
                    Mail::to($Application->company->office_email1)->send(new DeclineInternshipMail($Application));
                }
                if($Application->company->office_email2) {
                    Mail::to($Application->company->office_email2)->send(new DeclineInternshipMail($Application));
                }
                if($Application->company->office_email3) {
                    Mail::to($Application->company->office_email3)->send(new DeclineInternshipMail($Application));
                }
            }

            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new ApplicationsResource($Application),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Application Delete.
     * @group Application
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"message":"The record has been deleted successfully."}
     * */

    public function destroy($id)
    {
        try {
            // Delete the logged-in admin
            Applications::find($id)->delete();

            return $this->sendResponse([
                'message' => __('messages.deleted_success'),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function updateAdminRead(ApplicationsRequest $request) {
        $validatedData = $request->validated();
        try {
            Applications::whereIn('id', $validatedData['application_ids'])->update(['is_admin_read' => 1]);
            return $this->sendResponse([
                'counts' 	=> [
                    [
					'total_applied' => Applications::select('*')->where('status', 1)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 1)->where('is_admin_read', 0)->count(),
					],
					[
					'total_passed' => Applications::select('*')->where('status', 2)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 2)->where('is_admin_read', 0)->count(),
					],
                    [
					'total_completed' => Applications::select('*')->where('status', 3)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 3)->where('is_admin_read', 0)->count(),
					],
					[
					'total_failed' => Applications::select('*')->where('status', 4)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 4)->where('is_admin_read', 0)->count(),
					],
                    [
					'total_declined' => Applications::select('*')->where('status', 5)->count(),
					'admin_read' 	=> Applications::select('*')->where('status', 5)->where('is_admin_read', 0)->count(),
					],
                ],
                'message' => 'record updated'
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
