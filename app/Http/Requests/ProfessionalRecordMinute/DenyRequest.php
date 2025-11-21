<?php

namespace App\Http\Requests\ProfessionalRecordMinute;

class DenyRequest extends FindByIdRequest
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['rejection_note'] = 'nullable|string|max:255';
        return $rules;
    }
}
