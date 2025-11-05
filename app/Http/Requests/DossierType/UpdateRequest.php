<?php

namespace App\Http\Requests\DossierType;

use App\Http\Requests\DossierType\FindByIdRequest;

class UpdateRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'name' => 'required|string|max:255|unique:dossier_types,name,' . $this->id,
            ]
        );
    }
}
