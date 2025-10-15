<?php

namespace App\Http\Requests\ContractBill;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
            ],
            parent::rules()
        );
    }
}
