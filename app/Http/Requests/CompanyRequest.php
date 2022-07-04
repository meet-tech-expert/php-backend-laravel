<?php

namespace App\Http\Requests;

class CompanyRequest extends BaseFormRequest
{

    public function updateRules()
    {
        return $this->storeRules();
    }

    public function storeRules()
    {
        return [
            'business_industry_id' => ['nullable', 'exists:business_industries,id'],
            'name' => ['required'],
        ];
    }
}
