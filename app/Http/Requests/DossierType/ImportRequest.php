<?php

namespace App\Http\Requests\DossierType;

use App\Http\Requests\BaseRequest;

class ImportRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'file' => 'required|file|mimes:xlsx',
        ];
    }
}
