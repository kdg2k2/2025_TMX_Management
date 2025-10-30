<?php

namespace App\Http\Requests\UserTimeTable;

use App\Http\Requests\BaseRequest;

class GetWeeksRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'year' => 'required|integer',
        ];
    }
}
