<?php

namespace App\Http\Requests\LeaveRequest;

class AdjustRequest extends GetTotalLeaveDaysRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            app(ValidateTotalLeaveDaysRequest::class)->rules(),
        );
    }
}
