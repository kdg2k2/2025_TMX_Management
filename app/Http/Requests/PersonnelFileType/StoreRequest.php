<?php

namespace App\Http\Requests\PersonnelFileType;

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
            'name' => 'required|max:255|unique:personnel_file_types,name',
            'description' => 'nullable|max:255',
            'extensions' => 'required|array',
            'extensions.*' => 'required|exists:file_extensions,id',
        ];
    }
}
