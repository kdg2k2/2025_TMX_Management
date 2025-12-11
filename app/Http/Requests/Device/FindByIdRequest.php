<?php

namespace App\Http\Requests\Device;

use App\Http\Requests\BaseListRequest;

class FindByIdRequest extends BaseListRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:devices,id'
        ];
    }
}
