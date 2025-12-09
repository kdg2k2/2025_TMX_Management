<?php

namespace App\Http\Requests\Airport;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:airports,id'
        ];
    }
}
