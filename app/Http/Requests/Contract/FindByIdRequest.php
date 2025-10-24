<?php

namespace App\Http\Requests\Contract;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:contracts,id',
        ];
    }
}
