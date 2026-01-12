<?php

namespace App\Http\Requests;

class BaseFilterMonthYear extends BaseRequest
{
    public function rules(): array
    {
        return [
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer',
        ];
    }
}
