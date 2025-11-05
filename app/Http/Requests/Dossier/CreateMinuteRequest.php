<?php

namespace App\Http\Requests\Dossier;

use App\Http\Requests\Dossier\FindByContractIdAndYear;

class CreateMinuteRequest extends FindByContractIdAndYear
{
    public function rules()
    {
        $rules = parent::rules();
        unset($rules['nam']);
        return $rules;
    }
}
