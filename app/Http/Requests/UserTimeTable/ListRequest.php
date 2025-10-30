<?php

namespace App\Http\Requests\UserTimeTable;

use App\Http\Requests\BaseListRequest;
use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'week' => 'nullable|integer',
                'department_id' => 'nullable|exists:departments,id',
            ]
        );
    }
}
