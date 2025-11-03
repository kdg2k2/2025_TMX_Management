<?php

namespace App\Http\Requests\WorkTimesheet;

class ImportRequest extends ListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'file' => 'required|file|mimes:xlsx',
                'holiday_days' => 'nullable|array',
                'holiday_days.*' => 'nullable|date_format:Y-m-d',
                'power_outage_days' => 'nullable|array',
                'power_outage_days.*' => 'nullable|date_format:Y-m-d',
                'compensated_days' => 'nullable|array',
                'compensated_days.*' => 'nullable|date_format:Y-m-d',
            ]
        );
    }
}
