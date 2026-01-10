<?php

namespace App\Http\Requests\KasperskyCodeRegistration;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            //
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
