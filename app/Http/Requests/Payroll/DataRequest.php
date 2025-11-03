<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\BaseRequest;

class DataRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'year' => 'required|integer',
            'month' => 'required|integer',
        ];
    }
}
