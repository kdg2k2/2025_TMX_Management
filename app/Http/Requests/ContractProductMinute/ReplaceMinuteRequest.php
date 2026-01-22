<?php

namespace App\Http\Requests\ContractProductMinute;

use App\Http\Requests\BaseFindByIdRequest;

class ReplaceMinuteRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_product_minutes,id',
            'file_docx' => 'required|file|mimes:docx',
        ];
    }
}
