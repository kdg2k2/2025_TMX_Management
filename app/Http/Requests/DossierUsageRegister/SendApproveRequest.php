<?php

namespace App\Http\Requests\DossierUsageRegister;

use App\Http\Requests\Dossier\CreateMinuteRequest;

class SendApproveRequest extends CreateMinuteRequest
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['handover_date'] = 'required|date_format:Y-m-d';
        return $rules;
    }
}
