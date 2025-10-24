<?php

namespace App\Http\Requests\ProofContract;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:proof_contracts,id',
        ];
    }
}
