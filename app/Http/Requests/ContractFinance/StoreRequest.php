<?php

namespace App\Http\Requests\ContractFinance;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
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
