<?php

namespace App\Http\Requests\OfficialDocumentType;

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
            'created_by' => 'required|exists:users,id',
            'name' => 'required|max:255|unique:official_document_types,name',
            'description' => 'nullable|max:255',
        ];
    }
}
