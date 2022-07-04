<?php

namespace App\Http\Requests;

class ResetPasswordStudentRequest extends BaseFormRequest
{
    public function resetStudentPasswordRules()
    {
        return [
            'email_valid' => ['required', 'email', 'exists:students,email_valid'],
        ];
    }
}
