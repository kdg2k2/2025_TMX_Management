<?php

namespace App\Http\Requests\ProfessionalRecordUsageRegister;

use App\Http\Requests\ProfessionalRecord\CreateMinuteRequest;

class SendApproveRequest extends CreateMinuteRequest
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['handover_date'] = 'required|date_format:Y-m-d';
        return $rules;
    }
}
