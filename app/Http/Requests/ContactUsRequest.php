<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class ContactUsRequest extends BaseFormRequest
{
    protected function storeRules()
    {
        return [
            'company_or_school_name' => ['sometimes', 'nullable', 'string'],
            'email' => ['required', 'email'],
            'inquiry_content' => ['required', 'string'],
            'name' => ['required', 'string'],
            'telephone' => ['sometimes', 'nullable', 'string']
        ];
    }
}
