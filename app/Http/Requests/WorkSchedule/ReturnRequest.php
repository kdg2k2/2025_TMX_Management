<?php

namespace App\Http\Requests\WorkSchedule;

class ReturnRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return [
            'return_datetime' => 'required|date_format:Y-m-d H:i:s'
        ];
    }
}
