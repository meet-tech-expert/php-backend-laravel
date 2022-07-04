<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Actions\GetInternalId;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\InternshipPostResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\StudentsResource;
use App\Mail\EmailChangeMail;
use App\Mail\SignUpRequestMail;
use App\Models\Favorites;
use App\Models\Applications;
use App\Models\Feedbacks;
use App\Models\InternshipPosts;
use App\Models\Students;
use App\Models\SignupToken;
use App\Traits\InternshipPostTrait;
use FFI\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\StudentEmailChangeRequest;
use Carbon\Carbon;

class StudentController extends Controller
{
    use InternshipPostTrait;

    public function login(StudentRequest $request)
    {
        $requestedData = $request->validated();
        $student = Students::with(['educationFacility', 'applications'])->where('email_valid', '=', $requestedData['email'])->where('status', 1)->first();

        try {
            if ($student && $student->status == 3) {
                return $this->sendError(__('message.membership_cancelled'), 401);
            }

            if ($student && Hash::check($requestedData['password'], $student->password)) {
                $token = $student->createToken('student')->plainTextToken;

                return $this->sendResponse([
                    'token' => $token,
                    'student' => new StudentsResource($student),
                ]);
            }

            return $this->sendError(__('message.invalid_email_password'), 401);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([
            'logout' => 'Success',
        ]);
    }

    public function storeTokenCheck(Request $request)
    {
        try {
            $requestedData = $request;
            $token = $requestedData['token'];
            $signupToken = SignupToken::where('token', $token)->where('is_used', 0)->latest()->first();

            $expiryTime = $signupToken->created_at->diffInHours(Carbon::now());

            if ($signupToken && $expiryTime < 24) {
                    $student = Students::where('email_invalid', $signupToken->email)->first();
                    if ($student) {
                        return $this->sendResponse([
                            'message' => __('messages.record_created_successfully'),
                            'data' => new StudentsResource($student)
                        ]);
                    }
                    return $this->sendError(__('messages.email_already_registered'));
            }
            return $this->sendError(__('messages.data_not_found'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function signup(StudentRequest $request)
    {
        try {

            $requestedData = $request->validated();
            $student = Students::where('email_invalid', $requestedData['email_valid'])->first();
            $student->family_name = $requestedData['family_name'];
            $student->first_name = $requestedData['first_name'];
            $student->family_name_furigana = $requestedData['family_name_furigana'];
            $student->first_name_furigana = $requestedData['first_name_furigana'];
            $student->email_valid = $requestedData['email_valid'];
            $student->education_facility_id = $requestedData['education_facility_id'];
            $student->graduate_year = $requestedData['graduate_year'];
            $student->graduate_month = $requestedData['graduate_month'];
            $student->status = $requestedData['status'];
            $student->password = bcrypt($requestedData['password']);
            $student->save();
            $student->student_internal_id = GetInternalId::get_internal_student_id($student->id, $student->created_at);
            $student->save();
            $signupToken = SignupToken::where('email', $student->email_valid)->where('token',$requestedData['sing_up_token'])->where('is_used', 0)->latest()->first();
            $signupToken->is_used = true;
            $signupToken->save();

            // add cache favourites for the user
            if(isset($requestedData['cache_favorite'])) {
                $favorite = new Favorites();
                $favorite->internship_post_id = $requestedData['cache_favorite'];
                $favorite->student_id = $student->id;
                $favorite->status = 1;
                $favorite->save();
            }
            return $this->sendResponse([
                'message' => __('messages.update_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Student View.
     * @group Students
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.data_found","data":{"id":1,"family_name":"minhaz","first_name":"Ajamaef","family_name_furigana":"demo","first_name_furigana":"demo2","email_valid":"demo@gtmail.com","email_invalid":"demo@gtmail.com","is_email_approved":1,"education_facility_id":1,"graduate_year":2020,"graduate_month":"02","self_introduction":"30","status":1,"created_at":"2022-03-17T17:53:49.000000Z","updated_at":"2022-03-17T17:53:49.000000Z","deleted_at":null,"education_facilities":{"id":1,"name":"sdfds","type":0,"created_at":null,"updated_at":null,"deleted_at":null}}}}
     * */

    public function show($id)
    {
        try {
            $student = Students::with(['educationFacility', 'favorites'])->withCount(['feedbacks' => function($query) {
                $query->where('is_read', 0)->where('is_draft_or_public', 1);
            }])->findOrFail($id);
            if ($student) {
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => new StudentsResource($student),
                ]);
            }
            return $this->sendError(__('messages.data_not_found'));
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Student Create.
     * @group Students
     * @param Request $request
     * @bodyParam family_name string required Example: Rayhan
     * @bodyParam first_name string required Example: Raju
     * @bodyParam family_name_furigana string required Example: ASD
     * @bodyParam first_name_furigana string required Example: XYZ
     * @bodyParam email_valid email required Example: devraju.bd@gmail.com
     * @bodyParam email_invalid email required Example: devraju.bd@gmail.com
     * @bodyParam password string required Example: 12345678
     * @bodyParam is_email_approved boolean required Example: 1
     * @bodyParam education_facility_id integer Example: 1
     * @bodyParam graduate_year year Example: 2016
     * @bodyParam graduate_month string Example: 02
     * @bodyParam self_introduction string Example: Hello World
     * @bodyParam status integer  Example: 1
     * @bodyParam is_admin_read integer  0 for unread and 1 for read Example:
     * @return \Illuminate\Http\Response
     * @response 200 {"status":"Success","message":"Created Successfully","code":201,"data":{"family_name":"Rayhan","first_name":"nbxgcjzbtafuiu","family_name_furigana":"ktllbobqzviftazunfozppr","first_name_furigana":"rlyenpaddbu","email_valid":"ariane.corwin@example.net","email_invalid":"herzog.joy@example.org","is_email_approved":true,"education_facility_id":"1","graduate_year":null,"graduate_month":null,"self_introduction":"culpa","status":null,"updated_at":"2022-03-10T16:30:14.000000Z","created_at":"2022-03-10T16:30:14.000000Z","id":1}}
     */

    public function store(StudentRequest $request)
    {
        try {
            #NOTE: Validate input request
            $requestedData = $request->validated();
            $cancelmembershipCheck = Students::where('email_valid', $requestedData['email_invalid'])->first();
            if ($cancelmembershipCheck && $cancelmembershipCheck->status == '3') {
                $student = new Students();
                $student->email_invalid = $requestedData['email_invalid'];
                $student->is_email_approved = 0;
                $student->save();

                $token = Str::random(60);
                $signupToken = new SignupToken();
                $signupToken->email = $requestedData['email_invalid'];
                $signupToken->token = $token;
                $signupToken->save();
                $signUpRequestUrl =  env('USER_SITE_URL')."?token=" . $token;
                $data = [
                    'student' => $student,
                    'url' => $signUpRequestUrl,
                ];
                Mail::to($student->email_invalid)->send(new SignUpRequestMail($data));
                return $this->sendResponse([
                    'message' => __('messages.record_created_successfully'),
                    'data' => new StudentsResource($student),
                ]);
            } else if ($cancelmembershipCheck && ($cancelmembershipCheck->status == '' || $cancelmembershipCheck->status == '2')) {

                return $this->sendError(__('message.this_email_did_not_cancelmembership'), 401);
            } else {
                $student = new Students();
                $student->email_invalid = $requestedData['email_invalid'];
                $student->is_email_approved = 0;
                $student->save();

                $token = Str::random(60);
                $signupToken = new SignupToken();
                $signupToken->email = $requestedData['email_invalid'];
                $signupToken->token = $token;
                $signupToken->save();
                $signUpRequestUrl = env('USER_SITE_URL').'?token=' . $token;
                $data = [
                    'student' => $student,
                    'url' => $signUpRequestUrl,
                ];
                Mail::to($student->email_invalid)->send(new SignUpRequestMail($data));
                return $this->sendResponse([
                    'message' => __('messages.record_created_successfully'),
                    'data' => new StudentsResource($student),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Student Update.
     * @group Students
     * @param Request $request
     * @urlParam id integer required Example: 1
     * @bodyParam family_name string required Example: Rayhan
     * @bodyParam first_name string required Example: Raju
     * @bodyParam family_name_furigana string required Example: ASD
     * @bodyParam first_name_furigana string required Example: XYZ
     * @bodyParam email_valid email required Example: devraju.bd@gmail.com
     * @bodyParam email_invalid email required Example: devraju.bd@gmail.com
     * @bodyParam password string required Example: 12345678
     * @bodyParam is_email_approved boolean required Example: 1
     * @bodyParam education_facility_id integer Example: 1
     * @bodyParam graduate_year year Example: 2016
     * @bodyParam graduate_month string Example: 02
     * @bodyParam self_introduction string Example: Hello World
     * @bodyParam status boolean required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.update_success","data":{"id":1,"family_name":"ddd","first_name":"ddd","family_name_furigana":"dddd","first_name_furigana":"dddd","email_valid":"dd@gmail.com","email_invalid":"dddd@gmail.com","is_email_approved":"1","education_facility_id":"2","graduate_year":"2022","graduate_month":"02","self_introduction":"erererer","status":"1","created_at":"2022-03-17T17:53:49.000000Z","updated_at":"2022-03-17T18:08:25.000000Z","deleted_at":null}}}
     *
     **/

    public function update(StudentRequest $request, $id)
    {
        try {

            $requestedData = $request->validated();

            $student = Students::findOrFail($id);
            $student->family_name = $requestedData['family_name'] ?? $student->family_name;
            $student->first_name = $requestedData['first_name'] ?? $student->first_name;
            $student->family_name_furigana = $requestedData['family_name_furigana'] ?? $student->family_name_furigana;
            $student->first_name_furigana = $requestedData['first_name_furigana'] ?? $student->first_name_furigana;
            $student->email_invalid = $requestedData['email_invalid'] ?? $student->email_invalid;
            $student->education_facility_id = $requestedData['education_facility_id'] ?? $student->education_facility_id;
            $student->graduate_year = $requestedData['year'] ?? $student->graduate_year;
            $student->graduate_month = $requestedData['month'] ?? $student->graduate_month;
            $student->self_introduction = (array_key_exists( 'self_introduction' , $requestedData)) ? $requestedData['self_introduction'] : $student->self_introduction;
            $student->status = $requestedData['status'] ?? $student->status;
            $student->student_internal_id =  $student->student_internal_id != ""?$student->student_internal_id:GetInternalId::get_internal_student_id($student->id, $student->created_at);
            $student->save();
            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new StudentsResource($student),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function sendTokenEmailChange(StudentRequest $request, $id)
    {
        try {
            $requestedData = $request->validated();
            $student = Students::findOrFail($id);
            $checkEmailInRequestEmailToken = StudentEmailChangeRequest::where('email', $requestedData['email_invalid'])->first();
            if ($checkEmailInRequestEmailToken) {
                $token = $checkEmailInRequestEmailToken->token;
                $student->email_invalid = $requestedData['email_invalid'];
                $student->save();
                $emailChangeTokenUrl =  env('USER_SITE_URL') . '?emailtoken=' . $token;
                $data = [
                    'student_first_name' => $student->first_name,
                    'student_family_name' => $student->family_name,
                    'url' => $emailChangeTokenUrl,
                ];
                Mail::to($requestedData['email_invalid'])->send(new EmailChangeMail($data));
                $checkEmailInRequestEmailToken->is_used = 0;
                $checkEmailInRequestEmailToken->save();
            } else {
                $token = Str::random(60);
                $emailChangeToken = new StudentEmailChangeRequest();
                $emailChangeToken->email = $requestedData['email_invalid'];
                $emailChangeToken->token = $token;
                $emailChangeToken->save();
                $student->email_invalid = $requestedData['email_invalid'];
                $student->save();
                $emailChangeTokenUrl = env('USER_SITE_URL') . '?emailtoken=' . $token;
                $data = [
                    'student_first_name' => $student->first_name,
                    'student_family_name' => $student->family_name,
                    'url' => $emailChangeTokenUrl,
                ];
                Mail::to($requestedData['email_invalid'])->send(new EmailChangeMail($data));
            }
            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new StudentsResource($student),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
    //Email Change Request from Student from my profile
    public function storeTokenEmailChange(Request $request)
    {

        try {
            #NOTE: Validate input request
            $requestedData = $request;
            $token = $requestedData['token'];
            $requestEmailToken = StudentEmailChangeRequest::where('token', $token)->where('is_used', 0)->latest()->first();
            if ($requestEmailToken) {
                    $student = Students::where('email_invalid', $requestEmailToken->email)->first();
                    if ($student) {
                        $student->email_valid = $student->email_invalid;
                        $student->is_email_approved = 1;
                        $student->save();
                        #NOTE: token table Update
                        $requestEmailToken->is_used = 1;
                        $requestEmailToken->save();
                    }
            }
            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Student Delete.
     * @group Students
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200  {"data":{"message":"messages.deleted_success"}}
     **/

    public function delete($id)
    {
        try {
            // Delete the logged-in admin
            Students::find($id)->delete();

            return $this->sendResponse([
                'message' => __('messages.deleted_success'),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function studentIntershipPosts(Request $request)
    {
        try {
            // get all internship posts with work category
            $posts = $this->getInternshipPostListing($request);

            return $this->sendResponse([
                'message' => __('messages.show_all_success'),
                'data' => InternshipPostResource::collection($posts),
                'paginate' => new PaginationResource($posts),
                'counts' => [
                    'total_opened' => InternshipPosts::where('status', 0)->where('draft_or_public', $request->draft_or_public == 'draft' ? 0 : 1)->count(),
                    'total_ended' => InternshipPosts::where('status', 1)->where('draft_or_public', $request->draft_or_public == 'draft' ? 0 : 1)->count(),
                ],
            ]);
        } catch (Exception $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    public function updateMembership(Request $request, $userId)
    {
        try {

            $student = Students::where('id', $userId)->first();

            $data = [
                'status' => $request->status,
                'reason_for_withdrawal' => $request->reason,
                'email_valid' => $student->email_valid."_cancelled",
                'email_invalid' => $student->email_invalid."_cancelled"
            ];

            Students::where('id', $userId)->update($data);
			Applications::where('student_id', $userId)->delete();
			Feedbacks::where('student_id', $userId)->delete();
			Favorites::where('student_id', $userId)->delete();
            return $this->sendResponse([
                'message' => __('Update Membership.'),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
}
