<?php

namespace App\Http\Requests;

class FeedbackRequest extends BaseFormRequest
{
    public function indexRules()
    {
        return [

        ];
    }

    public function storeRules()
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'super_power_review' => ['required', 'digits_between:1,8'],
            'super_power_comment' => ['required', 'string'],
            'growth_idea_review' => ['required', 'digits_between:1,8'],
            'growth_idea_comment' => ['required', 'string'],
            'posted_month' => ['required', 'date_format:Y-m'],
            'is_draft_or_public' => ['required', 'boolean'],
        ];
    }

    public function updateRules()
    {
        return $this->storeRules();
    }

    public function massUpdateRules() {
        return [
            'feedback_ids' => ['required', 'array', 'exists:feedbacks,id']
        ];
    }
}
