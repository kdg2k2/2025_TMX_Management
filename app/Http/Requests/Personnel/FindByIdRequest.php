<?php

namespace App\Http\Requests\Personnel;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:personnels,id',
        ];
    }
}
