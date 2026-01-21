<?php

namespace App\Http\Requests\ContractProductInspection;

use Illuminate\Foundation\Http\FormRequest;

class CancelRequest extends FormRequest
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
