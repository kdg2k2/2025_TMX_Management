<?php

namespace App\Http\Requests\LeaveRequest;

use App\Http\Requests\BaseRequest;

class ValidateTotalLeaveDaysRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'total_leave_days' => 'required|numeric|min:0.5',
        ];
    }
}
