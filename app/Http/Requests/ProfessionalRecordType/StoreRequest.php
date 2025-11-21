<?php

namespace App\Http\Requests\ProfessionalRecordType;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:professional_record_types,name',
            'unit' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'quantity_limit' => 'required|integer|min:0',
            'created_by' => 'required|integer|exists:users,id',
        ];
    }
}
