<?php

namespace App\Http\Requests\WorkSchedule;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:work_schedules,id',
        ];
    }
}
