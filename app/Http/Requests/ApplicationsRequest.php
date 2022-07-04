<?php

namespace App\Http\Requests;

class ApplicationsRequest extends BaseFormRequest
{

    public function storeRules()
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'internship_post_id' => ['required', 'integer', 'exists:internship_posts,id'],
            'status' => ['integer', 'digits_between:1,5'],
            'status' => ['nullable', 'integer'],
            'is_admin_read' => ['nullable', 'boolean'],
            'self_introduction' => ['sometimes', 'nullable'],
        ];
    }

    public function updateRules()
    {
        return [
            'is_user_requested' => ['sometimes', 'nullable'],
            'student_id' => ['required_without:is_user_requested', 'integer', 'exists:students,id'], // Required without user requested
            'company_id' => ['required_without:is_user_requested', 'integer', 'exists:companies,id'], // Required without user requested
            'internship_post_id' => ['required', 'integer', 'exists:internship_posts,id'],
            'cancel_reason' => ['required_if::is_user_requested,1', 'nullable'],
            'status' => ['nullable', 'integer'],
            'is_admin_read' => ['nullable', 'boolean'],
        ];
    }

    public function updateAdminReadRules() {
        return [
            'application_ids' => 'required', 'array'
        ];
    }
}
