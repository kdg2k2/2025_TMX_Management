<?php

namespace App\Http\Requests\ContractFileType;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:contract_file_types,id',
            'description' => 'nullable|max:255',
            'extensions' => 'required|array',
            'extensions.*' => 'required|exists:file_extensions,id',
        ];
    }
}
