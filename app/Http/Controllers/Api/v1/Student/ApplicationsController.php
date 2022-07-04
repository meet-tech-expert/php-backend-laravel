<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Actions\GetInternalId;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationsRequest;
use App\Http\Resources\ApplicationsResource;
use App\Models\Applications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaginationResource;
use App\Mail\AdminDeclineInternshipMail;
use App\Mail\ApplyInternship;
use App\Mail\DeclineInternshipMail;
use App\Mail\StudentDeclineInternshipMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApplicationsController extends Controller
{

    public function index(Request $request)
    {
        $authUser = Auth::guard('students')->user();
        $application = Applications::select('*')->
        with(
            [
            'internshipPost.workCategory', 
            'internshipPost.internshipFeaturePosts',
            'internshipPost' => function($query) {
                $query->withCount(['favorites', 'applications']);
            }
            ])
            ->whereHas('internshipPost', function($query) {
                $query->where('draft_or_public', 1);
            })
            ->where('student_id',$authUser->id)->latest();

        $application = $request->input('paginate')
        ? $application->paginate($request->input('paginate', 25))
        : $application->get();

        try {

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => ApplicationsResource::collection($application),
                'paginate' => $request->input('paginate') ?  new PaginationResource($application): ''
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
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
            $authUser = Auth::guard('students')->user();

            $application = Applications::where('student_id', $authUser->id)
                ->where('internship_post_id', $requestedData['internship_post_id'])
                // ->where('status', 1) // If applied only
                ->first();

            if ($application) {
                return $this->sendResponse([
                    'message' => __('messages.record_already_exist'),
                ]);
            }

            $application = new Applications();
            $application->student_id = $authUser->id;
            $application->company_id = $requestedData['company_id'];
            $application->internship_post_id = $requestedData['internship_post_id'];
            $application->is_admin_read = 0;
            $application->status = 1;
            $application->save();
            $application->internal_application_id = GetInternalId::get_internal_application_id($requestedData['student_id'], $application->id);
            $application->save();
            $authUser->self_introduction = $requestedData['self_introduction'];
            $authUser->save();
            $application->load('company', 'student', 'internshipPost');
            if($application->company->office_email1) {
                Mail::to(config('constants.kt_admin_email'))->send(new ApplyInternship($application));
            }
            return $this->sendResponse([
                'message' => __('messages.success'),
            ]);
        } catch (\Exception$e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
    public function update(ApplicationsRequest $request)
    {
        try {
            $requestedData = $request->validated();

            $authUser = Auth::guard('students')->user();

            $application = Applications::with('company', 'student', 'internshipPost')->where('student_id', $authUser->id)
                ->where('internship_post_id', $requestedData['internship_post_id'])
                ->where('status', 1) // If applied only
                ->first();

            if (!$application) {
                return $this->sendResponse([
                    'message' => __('messages.application_already_cancelled')
                ]);
            }

            $application->cancel_reason = $requestedData['cancel_reason'];
            $application->status = 5;
            $application->save();
            if($application->company->office_email1) {
                Mail::to($application->company->office_email1)->send(new DeclineInternshipMail($application));
            }
            if($application->company->office_email2) {
                Mail::to($application->company->office_email2)->send(new DeclineInternshipMail($application));
            }
            if($application->company->office_email3) {
                Mail::to($application->company->office_email3)->send(new DeclineInternshipMail($application));
            }
            Mail::to($authUser->email_valid)->send(new StudentDeclineInternshipMail($application));
            $adminUser = config('constants.kt_admin_email');
            Mail::to($adminUser)->send(new AdminDeclineInternshipMail($application));
            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new ApplicationsResource($application)
            ]);
        } catch (\Throwable$th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

}
