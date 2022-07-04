<?php

namespace App\Http\Requests;

class MediaPostRequest extends BaseFormRequest
{
    public function indexRules()
    {
        return [
            'paginate' => ['sometimes', 'required', 'integer', 'gte:0'],
            'page' => ['sometimes', 'required', 'integer', 'gt:0'],
            'sort_by' => ['sometimes', 'required', 'string', 'in:id,updated_at,public_date,view_counts,display_order'],
            'sort_by_order' => ['required_with:sort_by', 'string', 'in:asc,desc'],
            'status' => ['sometimes', 'string', 'in:Y,N'],
            'is_draft' => ['sometimes', 'string', 'in:Y,N'],
        ];
    }

    public function storeRules()
    {
        return [
            'title' => 'required|string|max:100',
            'media_tag_id' => 'sometimes|array|min:1',
            'media_tag_id.*' => 'exists:media_tags,id',

            'summery' => 'sometimes|nullable|string',
            'seo_slug' => 'required|string|unique:media_posts,seo_slug',
            'seo_ogp' => 'sometimes|nullable|string',
            'seo_meta_description' => 'sometimes|nullable|string',
            'seo_featured_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'description' => 'sometimes|nullable|string',
            'is_draft' => 'sometimes|nullable|boolean',
            'status' => 'sometimes|boolean',
        ];
    }

    public function updateRules()
    {
        return [
            'title' => 'required|string|max:100',
            'media_tag_id' => 'sometimes|array|min:1',
            'media_tag_id.*' => 'exists:media_tags,id',

            'summery' => 'sometimes|nullable|string',
            'seo_slug' => 'required|string|unique:media_posts,seo_slug,' . $this->id,
            'seo_ogp' => 'sometimes|nullable|string',
            'seo_meta_description' => 'sometimes|nullable|string',
            'seo_featured_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'description' => 'sometimes|nullable|string',
            'is_draft' => 'sometimes|nullable|boolean',
            'status' => 'sometimes|boolean',
            'clear_image' => 'sometimes|required|integer|in:1,0'
        ];
    }

    public function recommendationRules() {
        return [
            'tags' => ['required', 'array'],
            'post_id' => ['required', 'exists:media_posts,id']
        ];
    }
	public function messages(){
		return [
			'seo_featured_image.image' => 'jpeg, png, jpg, gif のファイルを使用してください。',
		];
	}
}
