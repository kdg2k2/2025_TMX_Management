<?php

namespace App\Http\Requests\ContractIntermediateProduct;

use Illuminate\Foundation\Http\FormRequest;

class ExportRequest extends FormRequest
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
