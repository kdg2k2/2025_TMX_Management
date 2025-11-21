<?php

namespace App\Http\Requests\ProfessionalRecord;

use App\Http\Requests\ProfessionalRecord\FindByContractIdAndYear;

class CreateMinuteRequest extends FindByContractIdAndYear
{
    public function rules()
    {
        $rules = parent::rules();
        unset($rules['year']);
        return $rules;
    }
}
