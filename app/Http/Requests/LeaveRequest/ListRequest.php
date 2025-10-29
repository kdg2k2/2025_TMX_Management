<?php

namespace App\Http\Requests\LeaveRequest;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'created_by' => 'nullable|exists:users,id',
                'approval_status' => 'nullable|in:pending,approved,rejected',
                'adjust_approval_status' => 'nullable|in:none,pending,approved,rejected',
                'from_date' => 'nullable|date_format:Y-m-d',
                'to_date' => 'nullable|date_format:Y-m-d',
            ]
        );
    }
}
