<?php

namespace App\Http\Requests\WorkSchedule;

use Carbon\Carbon;

class ReturnRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'return_datetime' => Carbon::parse($this->return_datetime)->format('Y-m-d H:i'),
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'return_datetime' => 'required|date_format:Y-m-d H:i',
            ]
        );
    }
}
