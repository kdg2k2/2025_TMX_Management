<?php

namespace App\Http\Requests\TaskSchedule;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:task_schedules,id',
        ];
    }
}
