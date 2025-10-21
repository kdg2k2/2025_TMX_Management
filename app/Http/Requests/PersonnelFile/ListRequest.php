<?php

namespace App\Http\Requests\PersonnelFile;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'contract_id' => 'nullable|exists:contracts,id',
            'type_id' => 'nullable|exists:contract_scan_file_types,id',
        ]);
    }
}
