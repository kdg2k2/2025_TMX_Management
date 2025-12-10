<?php

namespace App\Http\Requests\Airline;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:airlines,id'
        ];
    }
}
