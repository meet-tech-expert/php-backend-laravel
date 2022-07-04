<?php

namespace App\Http\Requests;

class StudentRequest extends BaseFormRequest
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
            'email_invalid' => ['required', 'string', 'email', 'max:50',],
        ];
    }

    public function storeRules()
    {
        return [
             'email_invalid' => ['required','email','unique:students,email_valid'],
        ];
    }

    public function updateRules()
    {
        return [

            'family_name' => ['nullable','string','max:30'],
            'first_name' =>  ['nullable','string','max:30'],
            'family_name_furigana'=>  ['nullable','string','max:30'],
            'first_name_furigana'=>  ['nullable','string','max:30'],
            'email_valid' =>  ['nullable','email'],
            'education_facility_id' =>  ['nullable','integer','exists:education_facilities,id'],
            'year' =>  ['nullable','integer','digits:4','min:1900'],
            'month'=>  ['nullable','integer','digits_between:1,2'],
            'self_introduction' =>  ['nullable','string'],
            'is_admin_read' => ['nullable', 'integer'],
            'status'  =>  ['nullable','boolean'],

        ];
    }
    public function signupRules()
    {
        return [

            'family_name' => ['required', 'string', 'max:30'],
            'first_name' =>  ['required', 'string', 'max:30'],
            'family_name_furigana' =>  ['required', 'string', 'max:30'],
            'first_name_furigana' =>  ['required', 'string', 'max:30'],
            'email_valid' =>  ['required', 'email'],
            'education_facility_id' =>  ['required', 'integer', 'exists:education_facilities,id'],
            'graduate_year' =>  ['required', 'integer', 'digits:4', 'min:1900'],
            'graduate_month' =>  ['required', 'integer', 'digits_between:1,2'],
            'status'  =>  ['required', 'integer', 'digits_between:0,3'],
            'password' => ['required', 'string', 'min:8', 'regex:/(?=.{8,})((?=.*\d)(?=.*[a-z])(?=.*[A-Z])|(?=.*\d)(?=.*[a-zA-Z])(?=.*[\W_])|(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])).*/',],
            'cache_favorite' => ['nullable', 'exists:internship_posts,id'],
            'sing_up_token'  => ['nullable','string']
        ];
    }

    public function indexRules()
    {
        return [
            'paginate' => ['sometimes', 'integer', 'gte:0'],
            'page' => ['required_with:paginate', 'integer', 'gt:0'],
            'sort_by' => ['sometimes', 'string', 'in:id,created_at'],
            'sort_by_order' => ['required_with:sort_by', 'string', 'in:asc,desc'],
            'status' => ['sometimes', 'integer', 'digits_between:1,3'],
            'search' => ['sometimes', 'string'],
        ];
    }

    public function sendTokenEmailChangeRules()
    {
        return [
            'email_invalid' =>  ['required', 'email','unique:students,email_valid'],
        ];
    }

    public function messages()
    {
        return [
            'email_invalid.unique' => 'このメールアドレスを使用することはできません。',
        ];
    }
}
