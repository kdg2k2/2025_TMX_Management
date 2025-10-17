<?php

namespace App\Http\Requests\ContractFinance;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
            ]
        );
    }
}
