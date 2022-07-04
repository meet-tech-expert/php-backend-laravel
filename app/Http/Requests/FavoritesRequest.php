<?php

namespace App\Http\Requests;

class FavoritesRequest extends BaseFormRequest
{
    public function storeRules()
    {
        return [
            'student_id' => ['required', 'integer'],
            'internship_post_id' => ['required', 'integer'],
            'status' => ['required', 'integer'],
        ];
    }

}
