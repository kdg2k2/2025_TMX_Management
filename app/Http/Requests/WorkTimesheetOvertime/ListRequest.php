<?php

namespace App\Http\Requests\WorkTimesheetOvertime;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'year' => 'required|integer',
                'month' => 'required|integer',
            ]
        );
    }
}
