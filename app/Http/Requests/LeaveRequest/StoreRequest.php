<?php

namespace App\Http\Requests\LeaveRequest;

class StoreRequest extends GetTotalLeaveDaysRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'from_date' => 'required|date_format:Y-m-d|after_or_equal:today',
                'type' => 'required|in:one_day,many_days,morning,afternoon',
                'created_by' => 'required|exists:users,id',
                'reason' => 'required|max:255',
            ],
            app(ValidateTotalLeaveDaysRequest::class)->rules(),
        );
    }
}
