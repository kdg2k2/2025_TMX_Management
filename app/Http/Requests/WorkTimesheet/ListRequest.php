<?php

namespace App\Http\Requests\WorkTimesheet;

use App\Http\Requests\BaseRequest;

class ListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'year' => 'required|integer',
            'month' => 'required|integer',
        ];
    }
}
