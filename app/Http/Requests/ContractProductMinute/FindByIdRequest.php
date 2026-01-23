<?php

namespace App\Http\Requests\ContractProductMinute;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_product_minutes,id',
        ];
    }
}
