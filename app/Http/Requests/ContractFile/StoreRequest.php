<?php

namespace App\Http\Requests\ContractFile;

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
        $extensions = implode(',', app(\App\Services\ContractFileTypeService::class)->getExtensions($this->type_id) ?? []);
        if(empty($extensions))
            throw new \Exception("Loại file này chưa dược định nghĩa các loại định dạng");
        return [
            'contract_id' => 'required|exists:contracts,id',
            'type_id' => 'required|exists:contract_file_types,id',
            'created_by' => 'required|exists:users,id',
            'path' => "required|file|mimes:$extensions",
            'updated_content' => 'nullable|max:255',
        ];
    }
}
