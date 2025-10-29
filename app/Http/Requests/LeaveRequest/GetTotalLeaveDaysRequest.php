<?php

namespace App\Http\Requests\LeaveRequest;

use App\Http\Requests\BaseRequest;

class GetTotalLeaveDaysRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
            'type' => 'nullable|in:one_day,many_days,morning,afternoon',
        ];
    }
}
