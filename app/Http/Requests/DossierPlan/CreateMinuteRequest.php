<?php

namespace App\Http\Requests\DossierPlan;

class CreateMinuteRequest extends \App\Http\Requests\Dossier\CreateMinuteRequest
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['received_by'] = 'required|exists:users,id';
        $rules['handover_date'] = 'required|date_format:Y-m-d|after_or_equal:today';
        return $rules;
    }
}
