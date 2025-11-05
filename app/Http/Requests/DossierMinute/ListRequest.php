<?php

namespace App\Http\Requests\DossierMinute;

class ListRequest extends \App\Http\Requests\Dossier\ListRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['contract_id'], $rules['nam']);
        return $rules;
    }
}
