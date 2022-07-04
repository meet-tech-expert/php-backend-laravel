<?php

namespace App\Http\Requests;

class AdminRequest extends BaseFormRequest
{
    public function loginRules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ];
    }

    public function storeRules()
    {
        return [
            'name' => ['required', 'string', 'unique:admins,name'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ];
    }

    public function updateRules()
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function emailCheckRules()
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
    
}
