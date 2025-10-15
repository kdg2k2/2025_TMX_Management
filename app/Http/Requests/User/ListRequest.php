<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'department_id' => 'nullable|exists:departments,id',
                'position_id' => 'nullable|exists:positions,id',
                'job_title_id' => 'nullable|exists:job_titles,id',
            ]
        );
    }
}
