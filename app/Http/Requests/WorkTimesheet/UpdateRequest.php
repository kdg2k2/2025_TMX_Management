<?php

namespace App\Http\Requests\WorkTimesheet;

class UpdateRequest extends ListRequest
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
