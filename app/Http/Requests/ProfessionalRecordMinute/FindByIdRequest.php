<?php

namespace App\Http\Requests\ProfessionalRecordMinute;

use App\Http\Requests\BaseRequest;

class FindByIdRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:professional_record_minutes,id',
        ];
    }
}
