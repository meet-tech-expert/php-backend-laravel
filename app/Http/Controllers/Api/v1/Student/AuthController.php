<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentsResource;
use App\Http\Resources\Users\User as UserResource;
use App\Libraries\WebApiResponse;
use App\Models\Students;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Validator;
use App\Rules\MatchOldPassword;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        $requestedData = $request->validated();
        $student = Students::with('educationFacility')->where('email_valid', '=', $requestedData['email'])->first();
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

    public function logOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }

    public function changePassword(AuthRequest $request)
    {
        try {
            $requestedData = $request->validated();
            $student = Students::find(auth()->user()->id);
            $student->password = Hash::make($requestedData['password']);
            $student->save();
           
            return $this->sendResponse([
                'message' => __('messages.password  Change successfully '),
            ]);
        } catch (\Throwable $th) {
            return $this->sendApiLogsAndShowMessage($th);
        }
    }


    /**
     * Secrete Token
     * @group Authentication
     * @param  Request $request
     * @return Response
     * @response 200 {"status":"Success","message":"Login Success","code":200,"data":{"id":2,"username":"alamin12","email":"alamin20192019@gmail.com","sex":"Male","status":1,"industry_id":1,"salary_range_id":1,"referral_code":"2023","access_token":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNDg1Y2YzYTlkZGE0MDI4ODRmMDA4OTJhZDkyZDRmZmNiMjI0ZmFjNWVjOWQ0M2ZmZGNjMWQ5NWU3MGVkZGFkMzhjYzVjNDU0ODBiZmY3ZDUiLCJpYXQiOiIxNjE4NTA0MjIzLjI1ODk4NCIsIm5iZiI6IjE2MTg1MDQyMjMuMjU4OTg3IiwiZXhwIjoiMTYxODU5MDYyMy4xMTg5MDkiLCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.heiHePCYpVEsHsq8m-B1HgX_TVvecXDGGQdPaOIHA0Rbn98pFUIg7v73tk93IF1IxIp5VGMFDzpAlenHhdqI5sIpW5iuXxcLSa_XmtqCzxHOg_5C8m5KehW--tbnDtrKmE2G2563M52hX9TscqIYt17joQndpAi3pWCs4dklea5mls1UoXg8zPDkp5DkZ_tuORXCsXpldPVXw0JjuOuGmL0D5yfMg3Xufw-KdYX-Ip0GsgU2eT2xf7sjDQcAtba2I_4kNZbgr56ta2RmNxvDIg0spfhoRktQgEsnREesezpv8hyy_SiMHfChmAucyAqTh7TOoFjUZ2BzAqPRBuyw-nLUBogRPE2Y1S9OQg0Od1-Aqgi4F4EFSrklJHrCB5koOLgqoURt_RySo2SweEjIHdudMRSQhtXCRVeBBimxeH_9zIRH_T684hmluNCCIlZeYTxgRBBkFg65i9i1GUSjLpkciUu0txlpPevsOou68A_MWwWByGifq8o4GuY1NVY1FgMYkYMuGraGduUtsmaCBhVZlTX1-tPfuTR1k3XjM7RGMTp8C-8UTqwzruLDu20cwJXsQ4Of6Jp970Rl6FI-9CzqWhlx5GIxrvcFufkV2ntAApBllt4fBVKBKkaaJCCgLo6MrTRuKGU5aI1Lzcz1hkFWHw0JjMDN3jOS5cmD9OM","token_type":"Bearer","expires_at":"2021-04-16 16:30:23"},"profile_pic":"public\/upload\/60785f80cb084_1611049331368.JPEG","created_at":"2021-04-15T15:45:04.000000Z","updated_at":"2021-04-15T15:45:04.000000Z"}}
     */

    public function client_token($id)
    {
        $secret = DB::table('oauth_clients')->where('secret', 'id')->where('id', 2)->first();
        if ($secret) {
            return WebApiResponse::success(200, $secret->toArray(), trans('messages.success_show'));
        } else {
            return WebApiResponse::error(404, [], trans('messages.success_show_faild'));
        }
    }
}
