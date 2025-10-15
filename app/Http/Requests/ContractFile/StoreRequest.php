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
        $rules = [
            'contract_id' => 'required|exists:contracts,id',
            'type_id' => 'required|exists:contract_file_types,id',
            'created_by' => 'required|exists:users,id',
            'updated_content' => 'nullable|max:255',
        ];
        $type = app(\App\Services\ContractFileTypeService::class)->findById($this->type_id, false);
        if ($type['type'] == 'file') {
            $extensions = implode(',', app(\App\Services\ContractFileTypeService::class)->getExtensions($this->type_id) ?? []);
            if (empty($extensions))
                throw new \Exception('Loại file này chưa dược định nghĩa các loại định dạng');

            $rules['path'] = "required|file|mimes:$extensions";
        } else {
            $rules['path'] = 'required|url';
        }

        return $rules;
    }
}
