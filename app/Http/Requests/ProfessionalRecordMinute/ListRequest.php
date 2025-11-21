<?php

namespace App\Http\Requests\ProfessionalRecordMinute;

class ListRequest extends \App\Http\Requests\ProfessionalRecord\ListRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['contract_id'], $rules['year']);
        return $rules;
    }
}
