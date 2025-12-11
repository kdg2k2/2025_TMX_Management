<?php

namespace App\Http\Requests\Device;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'device_type_id' => 'nullable|exists:device_types,id',
                'current_status' => 'nullable|in:normal,broken,faulty,lost,loaned,under_repair,stored',
            ]
        );
    }
}
