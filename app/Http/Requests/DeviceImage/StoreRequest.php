<?php

namespace App\Http\Requests\DeviceImage;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'device_id' => 'required|exists:devices,id',
            'path' => 'required|array',
            'path.*' => 'file|mimes:png,jpg,jpeg,webp|max:5120',
        ];
    }
}
