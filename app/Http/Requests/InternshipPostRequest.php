<?php

namespace App\Http\Requests;


class InternshipPostRequest extends BaseFormRequest
{
    public function indexRules()
    {
        return [
            'paginate' => ['sometimes', 'required', 'integer', 'gte:0'],
            'page' => ['sometimes', 'required', 'integer', 'gt:0'],
            'sort_by' => ['sometimes', 'required', 'string', 'in:id,created_at,public_date,favorites_count,applications_count,display_order,public_date,updated_at'],
            'sort_by_order' => ['required_with:sort_by', 'string', 'in:asc,desc'],
            'status' => ['sometimes', 'string', 'in:Y,N,both'],
            'search' => ['sometimes', 'string'],
            'date_from' => ['sometimes', 'string'],
            'date_to' => ['required_with:date_from', 'string'],
            'work_id' => ['sometimes', 'integer'],
            'industry_id' => ['sometimes', 'integer'],
        ];
    }

    public function storeRules()
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'company_id' => ['required', 'numeric'],
            'work_category_id' => ['required_if:draft_or_public,1', 'numeric'],
            'period' => ['required_if:draft_or_public,1', 'numeric'],
            'workload' => ['required_if:draft_or_public,1', 'numeric'],
            'wage' => ['required_if:draft_or_public,1', 'numeric', 'max:7'],

            'internship_feature_id' => ['sometimes', 'array', 'min:1'],
            'internship_feature_id.*' => ['integer', 'exists:internship_features,id'],

            'target_grade' => ['required_if:draft_or_public,1', 'numeric'],
            'application_step_1' => ['required_if:draft_or_public,1', 'string', 'max:30'],

            'application_step_2' => ['sometimes', 'nullable', 'string', 'max:30'],
            'application_step_3' => ['sometimes', 'nullable', 'string', 'max:30'],
            'application_step_4' => ['sometimes', 'nullable', 'string', 'max:30'],
            'seo_slug' => ['required', 'string', 'max:255', 'unique:internship_posts,seo_slug'],
            'seo_ogp' => ['sometimes', 'nullable', 'string', 'max:255'],
            'seo_meta_description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'seo_featured_image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'description_corporate_profile' => ['sometimes', 'nullable', 'string'],
            'description_internship_content' => ['sometimes', 'nullable', 'string'],
            'draft_or_public' => ['sometimes', 'nullable', 'boolean'],
            'status' => ['sometimes', 'nullable', 'boolean'],
        ];
    }

    public function updateRules()
    {
        $rules =  $this->storeRules();
        $rules['seo_slug'] = ['required', 'string', 'max:255', 'unique:internship_posts,seo_slug,' . $this->id];
        return $rules;
    }
}
