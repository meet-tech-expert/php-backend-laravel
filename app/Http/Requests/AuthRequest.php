<?php

namespace App\Http\Requests;

class AuthRequest extends BaseFormRequest
{

    public function loginRules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
        ];
    }

    public function signupEmailRules()
    {
        return [
            'email_invalid' => ['required', 'string', 'email', 'max:50'],
        ];
    }
    
    public function storeRules()
    {
        return [
             'email_invalid' =>  ['required','email','unique:students,email_invalid'],
        ];
    }

    public function updateRules()
    {
        return [
            'family_name'           => ['required','string','max:30'],
             'first_name'            =>  ['required','string','max:30'],
             'family_name_furigana'  =>  ['required','string','max:30'],
             'first_name_furigana'   =>  ['required','string','max:30'],
             'email_valid'           =>  ['required','email'],
             'education_facility_id' =>  ['required','integer','exists:education_facilities,id'],
             'year'                  =>  ['required','integer','digits:4','min:1900'],
             'month'                =>  ['required','integer','digits_between:1,2'],
             'self_introduction'    =>  ['required','string'],
             'status'               =>  ['required','integer', 'digits_between:0,3'],
        ];
    }

    public function indexRules()
    {
        return [
            'paginate' => ['required', 'integer', 'gte:0'],
            'page' => ['required', 'integer', 'gt:0'],
            'sort_by' => ['sometimes', 'string', 'in:id,created_at'],
            'sort_by_order' => ['required_with:sort_by', 'string', 'in:asc,desc'],
            'status' => ['sometimes', 'integer', 'digits_between:1,3'],
            'search' => ['sometimes', 'string'],
        ];
    }

    public function changePasswordRules(){
        return [
            'password' => ['required','string', 'min:8', 'max:20'],
            'confirm_password'  =>  ['required','required','same:password'],
        ];
    }
}
