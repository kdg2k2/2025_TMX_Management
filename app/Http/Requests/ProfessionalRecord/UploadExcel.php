<?php

namespace App\Http\Requests\ProfessionalRecord;

class UploadExcel extends CreateMinuteRequest
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['file'] = 'required|file|mimes:xlsx';
        return $rules;
    }
}
