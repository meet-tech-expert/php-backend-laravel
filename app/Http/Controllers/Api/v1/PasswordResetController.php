<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordAdminRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ResetPasswordStudentRequest;
use App\Jobs\SendPasswordResetMail;
use App\Libraries\PasswordResetServices;
use App\Libraries\WebApiResponse;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Validator;
use App\Models\Students;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class PasswordResetController extends Controller {

    /**
    * Send Password Email For Student
    * @group  Authentication
    * @param  \Illuminate\Http\Request  $request
    * @bodyParam  email_valid  email required Example: student@domain.com
    * @response 200 {'data': {'message':'Reset link sent toy your email'}}
     */

    public function resetStudentPassword( ResetPasswordStudentRequest $request ) {
        $requestedData = $request->validated();
        try {
            $userType = PasswordResetServices::$userTypes[ 'Student' ];
            $user = Students::where( 'email_valid',  $requestedData[ 'email_valid' ] )->first();
            if ( !$user ) {

                return response()->json( [ 'error' => 'User not found with this user' ], 400 );
            }
            $user->email = $requestedData[ 'email_valid' ];
            PasswordResetServices::sendResetMail( $user, $userType );
            return $this->sendResponse( [
                'message' => __( 'Reset link sent  your email' ),
            ] );
        } catch ( \Exception $e ) {
            return response()->json( [ 'error' => $e->getMessage() ], 500 );
        }
    }

    /**
    * Send Password Email For Admin
    * @group  Authentication
    * @param  \Illuminate\Http\Request  $request
    * @bodyParam  email email required Example: admin@domain.com
    * @response 200 {'data': {'message':'Reset link sent toy your email'} }
        */

    public function resetAdminPassword( ResetPasswordAdminRequest $request ) {
        $requestedData = $request->validated();

        try {
            $userType = PasswordResetServices::$userTypes[ 'Admin' ];

            $user = Admin::where( 'email',  $requestedData[ 'email' ] )->first();

            if ( !$user ) {
                return response()->json( [ 'error' => 'User not found with this user' ], 400 );
            }

            $resetUrl = $requestedData['reset_url'];

            PasswordResetServices::sendResetMail( $user, $userType, $resetUrl );

            return $this->sendResponse( [
                'message' => __( 'Reset link sent  your email' ),
            ] );
        } catch ( \Exception $e ) {
            return response()->json( [ 'error' => $e->getMessage() ], 500 );
        }
    }

    /**
    * Reset Password
    * @group  Authentication
    * @param  \Illuminate\Http\Request  $request
    * @bodyParam  token string required Example: gk1pDZLFlKhbtqgzjweUijXJmX0tjOXeNccbBIF2KZQWrBP4Am
    * @bodyParam  password string required Example: 12345678
    * @bodyParam  password_confirmation string required Example: 12345678
    * @response 200 { 'status':'Success', 'message':'Reset link sent toy your email', 'code':200, 'data':[ 'Password Reset Was Successful' ]}
    */

    public function resetPassword( Request $request ) {

        try {
            $resetRequest = PasswordResetRequest::where( 'token', $request->token )->first();

            if ( $resetRequest->is_used ) {

                return response()->json( [ 'error' =>'Token already used. Please request for a new password reset link' ], 400 );
            }
            // TODO: Validate token expiry

            $now = new Carbon();
            $expired_at = new Carbon( $resetRequest->expired_at );

            if ( $expired_at < $now ) {

                return response()->json( [ 'error' =>'Token expired. Please request for a new password reset link' ], 400 );
            }
            if ( $resetRequest->user_type == PasswordResetServices::$userTypes[ 'Admin' ] ) {
                $user = Admin::find( $resetRequest->user_id );
            } else {
                $user = Students::find( $resetRequest->user_id );
            }
            if ( !$user ) {
                return response()->json( [ 'error' =>'User not found' ], 400 );
            }

            $user->password = bcrypt( $request->password );
            $user->save();

            $resetRequest->is_used = true;
            $resetRequest->save();

            return $this->sendResponse( [
                'message' => __( 'Password Reset Was Successful' ),
            ] );

        } catch ( \Throwable $th ) {
            return response()->json( [ 'error' =>'Something went wrong' ], 400 );

        }
    }
}
