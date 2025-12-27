<?php

namespace App\Http\Requests\DeviceLoan;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'device_id' => 'nullable|exists:devices,id',
                'created_by' => 'nullable|exists:users,id',
                'status' => 'nullable|in:pending,approved,rejected,returned',
            ]
        );
    }
}
