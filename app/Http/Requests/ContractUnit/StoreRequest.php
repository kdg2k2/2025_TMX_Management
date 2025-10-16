<?php

namespace App\Http\Requests\ContractUnit;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:contract_units,name',
            'address' => 'nullable|max:255',
        ];
    }
}
