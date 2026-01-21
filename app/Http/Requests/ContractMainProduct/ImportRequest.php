<?php

namespace App\Http\Requests\ContractMainProduct;

class ImportRequest extends ExportRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'file' => 'required|file|mimes:xlsx',
                'year' => 'nullable|integer',
            ]
        );
    }
}
