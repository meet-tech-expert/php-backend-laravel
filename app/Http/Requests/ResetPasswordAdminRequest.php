<?php

namespace App\Http\Requests;

class ResetPasswordAdminRequest extends BaseFormRequest
{
    public function resetAdminPasswordRules()
    {
        return [
            'email' => ['required','email','exists:admins,email'],
            'token' => ['required','string']
        ];
    }

}
