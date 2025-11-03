<?php

namespace App\Http\Requests\WorkTimesheetOvertime;

use App\Http\Requests\BaseRequest;

class TemplateRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'year' => 'required|integer',
            'month' => 'required|integer',
        ];
    }
}
