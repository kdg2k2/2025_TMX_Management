<?php

namespace App\Http\Requests\ContractProductMinuteSignature;

use Illuminate\Foundation\Http\FormRequest;

class SignRequest extends FormRequest
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
