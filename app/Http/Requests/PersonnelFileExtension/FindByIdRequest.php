<?php

namespace App\Http\Requests\PersonnelFileExtension;

use Illuminate\Foundation\Http\FormRequest;

class FindByIdRequest extends FormRequest
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
