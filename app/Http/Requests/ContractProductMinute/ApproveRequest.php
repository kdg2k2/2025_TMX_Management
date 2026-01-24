<?php

namespace App\Http\Requests\ContractProductMinute;

use App\Http\Requests\BaseApproveRequest;

class ApproveRequest extends BaseApproveRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
        );
    }
}
