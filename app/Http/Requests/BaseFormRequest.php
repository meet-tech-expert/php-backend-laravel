<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class BaseFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = $this->{$this->route()->getActionMethod() . 'Rules'}();

        array_walk($rules, function (&$value, $key) { // add 'bail' to all rules
            if (is_string($value)) {
                $value = explode('|', $value);
            }
            array_unshift($value, 'bail');
        });

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];
        $rules = $this->{$this->route()->getActionMethod() . 'Rules'}();

        foreach ($rules as $attr => $item) {
            $attributes[$attr] = Lang::has('validation.attributes.' . str_replace(['.*.', '.*'], '_', $attr)) ? __('validation.attributes.' . str_replace(['.*.', '.*'], '_', $attr)) : str_replace(['.*.', '.*', '_'], ' ', $attr);
        }

        return $attributes;
    }
}
