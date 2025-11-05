<?php

namespace App\Http\Requests\DossierMinute;

use Illuminate\Foundation\Http\FormRequest;

class AcceptRequest extends FindByIdRequest
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['approval_note'] = 'nullable|string|max:255';
        return $rules;
    }
}
