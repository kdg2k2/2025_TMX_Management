<?php

namespace App\Http\Requests\ContractScanFileType;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_scan_file_types,id',
        ];
    }
}
