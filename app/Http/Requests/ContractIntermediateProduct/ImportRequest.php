<?php

namespace App\Http\Requests\ContractIntermediateProduct;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
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
