<?php

namespace App\Http\Requests\WorkTimesheet;

use App\Http\Requests\BaseRequest;

class ImportRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'year' => 'required|integer',
            'month' => 'required|integer',
            'file' => 'required|file|mimes:xlsx',
            'holiday_days' => 'nullable|array',
            'holiday_days.*' => 'nullable|date_format:Y-m-d',
            'power_outage_days' => 'nullable|array',
            'power_outage_days.*' => 'nullable|date_format:Y-m-d',
            'compensated_days' => 'nullable|array',
            'compensated_days.*' => 'nullable|date_format:Y-m-d',
        ];
    }
}
