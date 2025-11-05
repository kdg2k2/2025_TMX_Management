<?php

namespace App\Http\Requests\DossierType;

use App\Http\Requests\BaseRequest;

class FindByIdRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:dossier_types,id',
        ];
    }
}
