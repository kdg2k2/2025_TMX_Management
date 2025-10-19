<?php

namespace App\Http\Requests\ContractScanFileType;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'name' => 'required|max:255|unique:contract_scan_file_types,id,' . $this->id,
            ],
            parent::rules()
        );
    }
}
