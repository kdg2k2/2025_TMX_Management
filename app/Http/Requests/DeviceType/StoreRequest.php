<?php

namespace App\Http\Requests\DeviceType;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:device_types,name',
        ];
    }
}
