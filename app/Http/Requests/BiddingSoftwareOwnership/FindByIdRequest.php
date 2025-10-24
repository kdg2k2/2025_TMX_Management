<?php

namespace App\Http\Requests\BiddingSoftwareOwnership;

use App\Http\Requests\BaseRequest;

class FindByIdRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:bidding_software_ownerships,id',
        ];
    }
}
