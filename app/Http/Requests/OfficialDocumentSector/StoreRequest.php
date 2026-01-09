<?php

namespace App\Http\Requests\OfficialDocumentSector;

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
            'name' => 'required|max:255|unique:official_document_sectors,name',
            'description' => 'nullable|max:255',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ];
    }
}
