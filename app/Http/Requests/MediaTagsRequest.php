<?php

namespace App\Http\Requests;

class MediaTagsRequest extends BaseFormRequest
{
  
    public function storeRules()
    {
        return [

            'name' => ['required', 'string', 'unique:media_tags,name', 'max:50' ],
        ];
    }

    public function updateRules()
    {
        return [

                'name' => ['required', 'string', 'unique:media_tags,name,'. $this->id, 'max:50'],
         
        ];
    }

    public function indexRules()
    {
        return [
            'paginate' => ['required', 'integer', 'gte:0'],
            'page' => ['required', 'integer', 'gt:0'],
            'sort_by' => ['sometimes', 'string', 'in:id,created_at,name'],
            'sort_by_order' => ['required_with:sort_by', 'string', 'in:asc,desc'],
            'search' => ['sometimes', 'string'],
        ];
    }
}
