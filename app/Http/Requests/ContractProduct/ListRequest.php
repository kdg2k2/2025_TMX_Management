<?php

namespace App\Http\Requests\ContractProduct;

class ListRequest extends \App\Http\Requests\Contract\ListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'contract_product_minute_status' => 'nullable|in:draft,request_sign,request_approve,approved,rejected',
            ]
        );
    }
}
