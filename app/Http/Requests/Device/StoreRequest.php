<?php

namespace App\Http\Requests\Device;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'device_type_id' => 'required|exists:device_types,id',
            'name' => 'required|max:255',
            'seri' => 'nullable|max:255',
            'current_status' => 'required|in:normal,broken,faulty,lost,loaned,under_repair,stored',
            'current_location' => 'nullable|max:255',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
