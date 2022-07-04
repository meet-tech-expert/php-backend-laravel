<?php

namespace App\Http\Requests;

class EducationFacilitiesRequest extends BaseFormRequest
{


    public function storeRules()
    {
        return [

            'name'  => ['required', 'string', 'unique:education_facilities,name' ],
             'type'  => ['required', 'numeric'],
        ];
    

    }

    public function updateRules()
    {
        return [

            'name' => ['required', 'string'],
            'type' => ['required', 'numeric'],
     
    ];

    }
}
