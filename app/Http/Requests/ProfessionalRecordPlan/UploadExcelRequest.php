<?php

namespace App\Http\Requests\ProfessionalRecordPlan;

use App\Http\Requests\ProfessionalRecord\UploadExcel;

class UploadExcelRequest extends UploadExcel
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['received_by'] = 'required|exists:users,id';
        $rules['handover_date'] = 'required|date_format:Y-m-d|after_or_equal:today';
        return $rules;
    }
}
