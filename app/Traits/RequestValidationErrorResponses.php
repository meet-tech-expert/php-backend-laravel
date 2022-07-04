<?php


namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use App\Libraries\WebApiResponse;

trait RequestValidationErrorResponses
{
    public function failedValidation(Validator $validator) {
        return WebApiResponse::error(422, [$validator->errors()], trans('Something Went Wrong'));

    }
}
