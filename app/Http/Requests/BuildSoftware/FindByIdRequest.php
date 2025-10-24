<?php

namespace App\Http\Requests\BuildSoftware;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:build_software,id',
        ];
    }
}
