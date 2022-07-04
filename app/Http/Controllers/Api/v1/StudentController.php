<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Actions\GetInternalId;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentsResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Models\Students;
use App\Models\Applications;
use App\Models\Favorites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\SignUpRequestMail;
use Illuminate\Support\Facades\Mail;
use App\Models\InternshipPosts;
use App\Http\Resources\InternshipPostResource;
use App\Models\Feedbacks;
Use App\Traits\InternshipPostTrait;
use FFI\Exception;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    use InternshipPostTrait;


    public function login(StudentRequest $request)
    {
        $requestedData = $request->validated();
        $student = Students::with('educationFacility')->where('email_valid', '=', $requestedData['email'])->where('status',1)->first();

        try {
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



    /**
     * Student List.
     * @group Students
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @response 200 {"data":{"message":"messages.show_all_success","data":[{"id":1,"family_name":"minhaz","first_name":"Ajamaef","family_name_furigana":"demo","first_name_furigana":"demo2","email_valid":"demo@gtmail.com","email_invalid":"demo@gtmail.com","is_email_approved":1,"education_facility_id":1,"graduate_year":2020,"graduate_month":"02","self_introduction":"30","status":1,"created_at":"2022-03-17T17:53:49.000000Z","updated_at":"2022-03-17T17:53:49.000000Z","deleted_at":null,"education_facilities":{"id":1,"name":"sdfds","type":0,"created_at":null,"updated_at":null,"deleted_at":null}},{"id":2,"family_name":"minhaz","first_name":"Ajamaef","family_name_furigana":"demo","first_name_furigana":"demo2","email_valid":"demo@gtmail.com","email_invalid":"demo@gtmail.com","is_email_approved":1,"education_facility_id":1,"graduate_year":2020,"graduate_month":"02","self_introduction":"30","status":1,"created_at":"2022-03-17T17:55:46.000000Z","updated_at":"2022-03-17T17:55:46.000000Z","deleted_at":null,"education_facilities":{"id":1,"name":"sdfds","type":0,"created_at":null,"updated_at":null,"deleted_at":null}},{"id":3,"family_name":"minhaz","first_name":"Ajamaef","family_name_furigana":"demo","first_name_furigana":"demo2","email_valid":"demo@gtmail.com","email_invalid":"demo@gtmail.com","is_email_approved":1,"education_facility_id":1,"graduate_year":2020,"graduate_month":"02","self_introduction":"30","status":1,"created_at":"2022-03-17T17:56:10.000000Z","updated_at":"2022-03-17T17:56:10.000000Z","deleted_at":null,"education_facilities":{"id":1,"name":"sdfds","type":0,"created_at":null,"updated_at":null,"deleted_at":null}}]}}
     */

    public function index(StudentRequest $request)
    {
        #NOTE: Validate input request
        $requestedData = $request->validated();
        $totalStudentInactive = Students::where('status', '=', 2)->whereNotNull('email_valid')->whereNotNull('student_internal_id')->count();
        $totalStudentActive   = Students::where('status', '=', 1)->whereNotNull('email_valid')->whereNotNull('student_internal_id')->count();
        $membershipCancelled   = Students::where('status', '=', 3)->whereNotNull('email_valid')->whereNotNull('student_internal_id')->count();
        $student = Students::with('educationFacility');
        if ($requestedData['sort_by'] &&  $requestedData['sort_by_order']) {
            $student->orderBy($requestedData['sort_by'], $requestedData['sort_by_order']);
        }
        if (isset($requestedData['search'])) {
            $search = $requestedData['search'];
            $student->where(function ($query) use ($search) {
                $query->whereHas('educationFacility', function($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhere('email_valid', 'LIKE', "%{$search}%")
                    ->orWhere('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('student_internal_id', 'LIKE', "%{$search}%")
                    ->orWhere('family_name', 'LIKE', "%{$search}%")
                    ->orWhere('family_name_furigana', 'LIKE', "%{$search}%")
                    ->orWhere('first_name_furigana', 'LIKE', "%{$search}%");
            });
        }
        $student->where('status', '=', $requestedData['status'] ?? 1);
        $student->whereNotNull('email_valid');
        $student->whereNotNull('student_internal_id');

        $student = $request->paginate
            ? $student->paginate($request->paginate)
            : $student->get();
        return $this->sendResponse(
            [
                'students'            => StudentsResource::collection($student),
                'totalStudentInactive' => $totalStudentInactive,
                'totalStudentActive'   => $totalStudentActive,
                'membershipCancelled'  => $membershipCancelled
            ],
            $request->paginate
                ? ['paginate' => new PaginationResource($student)]
                : []
        );
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
            $Student = Students::with('educationFacility')->findOrFail($id);

            if ($Student) {
                return $this->sendResponse([
                    'message' => __('messages.data_found'),
                    'data' => new StudentsResource($Student),
                ]);
            }

            return $this->sendError(__('messages.data_not_found'));
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }
    public function create()
    {
        $Student = new Students();
        return $Student;
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

            $student = new Students();
            $student->email_invalid = $requestedData['email_invalid'];
            $student->is_email_approved = 0;
            $student->save();

            $student->student_internal_id = GetInternalId::get_internal_student_id($student->id, $student->created_at);
            $student->save();
            $signUpRequestUrl = env('SIGN-UP-REQUEST-MAIL') ;
            $data =[
                'student'=>$student,
                'url'=>$signUpRequestUrl
            ];

            Mail::to($student->email_invalid)->send(new SignUpRequestMail($data));
            return $this->sendResponse([
                'message' => __('messages.record_created_successfully'),
                'data' => new StudentsResource($student),
            ]);
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
            $student->family_name = $requestedData['family_name'];
            $student->first_name = $requestedData['first_name'];
            $student->family_name_furigana = $requestedData['family_name_furigana'];
            $student->first_name_furigana = $requestedData['first_name_furigana'];
            if($student->email_valid === $requestedData['email_valid'] || !Students::where('email_valid', '=', $requestedData['email_valid'])->exists()){
                $student->email_valid = $requestedData['email_valid'];
            } else {
                throw new Exception("Email already exists", 500);
            }
            $student->education_facility_id = $requestedData['education_facility_id'];
            $student->graduate_year = $requestedData['year'];
            $student->graduate_month = $requestedData['month'];
            $student->self_introduction = $requestedData['self_introduction'];
            $student->status = $requestedData['status'] ? 1 : 2;
            $student->is_admin_read = $requestedData['is_admin_read'];
            $student->save();
            //$student->student_internal_id = GetInternalId::get_internal_student_id($student->id);
           // $student->save();

            return $this->sendResponse([
                'message' => __('messages.update_success'),
                'data' => new StudentsResource($student),
            ]);
        } catch (\Throwable $th) {

            return $this->sendApiLogsAndShowMessage($th);
        }
    }

    /**
     * Student Delete.
     * @group Students
     * @urlParam id integer required Example: 1
     * @return \Illuminate\Http\Response
     * @response 200  {"data":{"message":"messages.deleted_success"}}
     **/

    public function destroy($id)
    {
        try {
            // Delete the student
            $student = Students::find($id);

            $student->email_valid = $student->email_valid . '_deleted';
            $student->email_invalid = $student->email_invalid.'_deleted';
            $student->save();

            // Now delete the student user.
            $student->delete();
			Applications::where('student_id', $id)->delete();
			Feedbacks::where('student_id', $id)->delete();
			Favorites::where('student_id', $id)->delete();
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

    public function studentFeedback(Students $student) {
        $reviews = $this->generateRawSelectSql(["super_power_review", "growth_idea_review"]);
        $feedbacks = Feedbacks::withTrashed()->select(DB::raw($reviews))
                    ->where('student_id',$student->id)
                    ->where('is_draft_or_public', 1)
                    ->first();
        $companies = Feedbacks::withTrashed()->select('company_id')->with('companies')->where('student_id', $student->id)->where('is_draft_or_public', 1)->distinct()->get();
        $comments = [];
        foreach($companies as $company) {
            $comment = Feedbacks::withTrashed()->select('super_power_comment', 'growth_idea_comment','super_power_review', 'growth_idea_review','posted_month', 'id')
                       ->where('company_id', $company->company_id)
                       ->where('student_id', $student->id)
                       ->where('is_draft_or_public', 1)
                       ->get();
            $temp = ['company_info' => $company->companies, 'comments' => $comment];
            array_push($comments,$temp);
        }
        return $this->sendResponse([
            'feedbacks' => $feedbacks,
            'comments' => $comments
        ]);
        return '';
    }
    public function generateRawSelectSql($columnNames) {
        $reviews = config("constants.reviews_option");
        $statement = [];
        foreach($columnNames as $columnName) {
            foreach($reviews as $element) {
                array_push($statement,"SUM(IF({$columnName} = {$element['id']}, 1, 0)) AS {$columnName}_{$element['id']}");
            }
        }
        return implode(",", $statement);
    }
}
