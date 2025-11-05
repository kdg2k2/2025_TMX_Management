<?php

namespace App\Http\Requests\DossierMinute;

use App\Http\Requests\BaseRequest;

class FindByIdRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:dossier_minutes,id',
        ];
    }
}
