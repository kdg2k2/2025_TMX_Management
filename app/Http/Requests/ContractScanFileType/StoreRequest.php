<?php

namespace App\Http\Requests\ContractScanFileType;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'extensions' => $this->extensions ?? [],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:contract_scan_file_types,id',
            'description' => 'nullable|max:255',
            'extensions' => 'required|array',
            'extensions.*' => 'required|exists:file_extensions,id',
        ];
    }
}
