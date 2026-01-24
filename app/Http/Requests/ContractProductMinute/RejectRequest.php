<?php

namespace App\Http\Requests\ContractProductMinute;

use App\Http\Requests\BaseRejectRequest;

class RejectRequest extends BaseRejectRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
        );
    }
}

