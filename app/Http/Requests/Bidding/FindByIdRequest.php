<?php

namespace App\Http\Requests\Bidding;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:biddings,id',
        ];
    }
}
