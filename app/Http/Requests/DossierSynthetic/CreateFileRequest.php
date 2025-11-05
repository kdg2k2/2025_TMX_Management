<?php

namespace App\Http\Requests\DossierSynthetic;

use App\Http\Requests\Dossier\FindByContractIdAndYear;

class CreateFileRequest extends FindByContractIdAndYear
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['contract_id'] = 'nullable|exists:contracts,id';
        return $rules;
    }
}
