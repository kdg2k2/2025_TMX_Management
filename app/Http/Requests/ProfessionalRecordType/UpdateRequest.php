<?php

namespace App\Http\Requests\ProfessionalRecordType;

use App\Http\Requests\ProfessionalRecordType\FindByIdRequest;

class UpdateRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'name' => 'required|string|max:255|unique:professional_record_types,name,' . $this->id,
            ]
        );
    }
}
