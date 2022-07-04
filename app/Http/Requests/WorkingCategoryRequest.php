<?php

namespace App\Http\Requests;

class WorkingCategoryRequest extends BaseFormRequest
{

    public function storeRules()
    {

        // name and display order should be unique
        return [
            'name' => 'required|string|unique:work_categories,name',
            'display_order' => 'nullable|integer|gt:0',
        ];
    }

    public function updateRules()
    {
        return [
            'name' => 'sometimes|string',
            'display_order' => 'nullable|integer|gt:0',
        ];
    }

    public function indexRules()
    {
        return [
            'paginate' => ['required', 'integer', 'gte:0'],
            'page' => ['required', 'integer', 'gt:0'],
            'sort_by' => ['sometimes', 'string', 'in:id,created_at,display_order'],
            'sort_by_order' => ['required_with:sort_by', 'string', 'in:asc,desc'],
            'search' => ['sometimes', 'string'],
        ];
    }
}
