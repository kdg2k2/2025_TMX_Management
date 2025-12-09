<?php

namespace App\Http\Requests\Airport;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:airports,name'
        ];
    }
}
