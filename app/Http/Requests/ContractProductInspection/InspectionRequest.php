<?php

namespace App\Http\Requests\ContractProductInspection;

use App\Http\Requests\BaseRequest;

class InspectionRequest extends BaseRequest
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
            'contract_id' => 'required|exists:contracts,id',
            'supported_by' => 'required|exists:users,id',
            'issue_file_path' => 'nullable|file|mimes:docx,xlsx,rar,zip|max:10240',
            'support_description' => 'nullable|max:255',
            'note' => 'nullable|max:255',
        ];
    }
}
