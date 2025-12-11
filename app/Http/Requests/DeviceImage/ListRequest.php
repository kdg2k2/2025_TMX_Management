<?php

namespace App\Http\Requests\DeviceImage;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'device_id' => 'required|exists:devices,id',
            ]
        );
    }
}
