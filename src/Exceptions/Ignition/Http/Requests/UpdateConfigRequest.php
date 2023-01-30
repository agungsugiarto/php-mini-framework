<?php

namespace Mini\Framework\Exceptions\Ignition\Http\Requests;

use Illuminate\Validation\Rule;
use Mini\Framework\Http\FormRequest;

class UpdateConfigRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'theme' => ['required',  Rule::in(['light', 'dark', 'auto'])],
            'editor' => ['required'],
            'hide_solutions' => ['required', 'boolean'],
        ];
    }
}
