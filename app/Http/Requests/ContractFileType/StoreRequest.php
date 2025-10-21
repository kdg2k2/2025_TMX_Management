<?php

namespace App\Http\Requests\ContractFileType;

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
        $rules = [
            'name' => 'required|max:255|unique:contract_file_types,name',
            'description' => 'nullable|max:255',
            'type' => 'required|in:file,url',
        ];

        $isUrl = $this->type == 'url';
        if ($isUrl == true)
            $this->extensions = [];
        $rules['extensions'] = $isUrl ? 'nullable' : 'required' . '|array';
        $rules['extensions.*'] = $isUrl ? 'nullable' : 'required' . '|exists:file_extensions,id';
        return $rules;
    }
}
