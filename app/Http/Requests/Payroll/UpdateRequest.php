<?php

namespace App\Http\Requests\Payroll;

class UpdateRequest extends DataRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'file' => 'required|file|mimes:xlsx',
            ]
        );
    }
}
