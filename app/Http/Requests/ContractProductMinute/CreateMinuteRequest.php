<?php

namespace App\Http\Requests\ContractProductMinute;

use Illuminate\Foundation\Http\FormRequest;

class CreateMinuteRequest extends FormRequest
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
