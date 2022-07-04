<?php 

namespace App\Libraries;

use App\Jobs\SendPasswordResetMail;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Str;

class PasswordResetServices{
    public static $userTypes = [
        'Admin' => 'Admin', 
        'Student' => 'Student' 
    ];

    public static function checkUserExistance($type, $email){
        return false;
    }

    public static function sendResetMail($user, $userType, $resetUrl){
        
        try {
            $now = $now = new Carbon();
            $now->addHours(24);

            $passwordResetRequest = new PasswordResetRequest();

            $passwordResetRequest->user_type = $userType;
            $passwordResetRequest->user_id = $user->id;
            $passwordResetRequest->token = Str::random(50);
            $passwordResetRequest->email   = $user->email_valid ?? $user->email;
            $passwordResetRequest->expired_at = $now->toDateTimeString();

            $passwordResetRequest->save();

            $data = [
                'user'  => $user, 
                'email' => $user->email, 
                'token' => $passwordResetRequest->token, 
                'url' => $resetUrl.'?token='.$passwordResetRequest->token
            ];

            dispatch(new SendPasswordResetMail($data));
        } catch (\Throwable $th) {
            \Log::info($th);
        }
    }
}