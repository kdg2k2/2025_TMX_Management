<?php

namespace App\Http\Requests\BinddingSoftwareOwnership;

use App\Http\Requests\BaseRequest;

class FindByIdRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:bindding_software_ownerships,id',
        ];
    }
}
