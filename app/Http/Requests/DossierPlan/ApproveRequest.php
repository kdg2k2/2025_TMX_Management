<?php

namespace App\Http\Requests\DossierPlan;

use App\Http\Requests\BaseRequest;

class ApproveRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'contract_id' => 'required|exists:contracts,id',
        ];
    }
}
