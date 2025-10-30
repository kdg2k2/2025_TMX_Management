<?php

namespace App\Http\Requests\UserWarning;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
            'warning_date' => 'required|date_format:Y-m-d',
            'type' => 'required|in:job,work_schedule',
            'work_schedule_id' => 'nullable|exists:work_schedules,id',
        ];
    }
}
