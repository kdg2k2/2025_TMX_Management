<?php

namespace App\Http\Requests\ContractFile;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_files,id',
        ];
    }
}
