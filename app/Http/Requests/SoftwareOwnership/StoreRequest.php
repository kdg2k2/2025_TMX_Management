<?php

namespace App\Http\Requests\SoftwareOwnership;

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
            'name' => 'required|max:255|unique:software_ownerships,name',
            'path' => 'required|file|mimes:pdf',
        ];
    }
}
