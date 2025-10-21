<?php

namespace App\Http\Requests\Eligibility;

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
            'name' => 'required|max:255|unique:eligibilities,name',
            'path' => 'required|file|mimes:pdf',
        ];
    }
}
