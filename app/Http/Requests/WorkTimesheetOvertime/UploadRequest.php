<?php

namespace App\Http\Requests\WorkTimesheetOvertime;

class UploadRequest extends ListRequest
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
