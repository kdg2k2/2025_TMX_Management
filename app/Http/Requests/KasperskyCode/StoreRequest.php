<?php

namespace App\Http\Requests\KasperskyCode;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'code' => 'required|max:255|unique:kaspersky_codes,code',
            'total_quantity' => 'required|integer|min:1',
            'valid_days' => 'required|integer|min:1',
            'path' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:5120',
        ];
    }
}
