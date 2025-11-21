<?php

namespace App\Http\Requests\ProfessionalRecordSynthetic;

use App\Http\Requests\ProfessionalRecord\FindByContractIdAndYear;

class CreateFileRequest extends FindByContractIdAndYear
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['contract_id'] = 'nullable|exists:contracts,id';
        return $rules;
    }
}
