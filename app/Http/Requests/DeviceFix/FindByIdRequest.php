<?php

namespace App\Http\Requests\DeviceFix;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:device_fixes,id',
        ];
    }
}
