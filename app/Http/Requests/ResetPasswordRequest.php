<?php

namespace App\Http\Requests;

class ResetPasswordRequest extends BaseFormRequest
{
    public function resetPasswordRules()
    {
        return [
            'token' => ['required', 'string','max:50','exists:password_reset_requests,token'],
            'password' => ['required','string','confirmed','min:8']
        ];
    }

}
