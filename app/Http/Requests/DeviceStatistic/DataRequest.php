<?php

namespace App\Http\Requests\DeviceStatistic;

use App\Http\Requests\BaseRequest;

class DataRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'year' => 'nullable|integer',
            'month' => 'nullable|integer',
        ];
    }
}
