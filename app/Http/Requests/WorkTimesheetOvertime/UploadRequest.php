<?php

namespace App\Http\Requests\WorkTimesheetOvertime;

class UploadRequest extends TemplateRequest
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
