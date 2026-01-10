<?php

namespace App\Http\Requests\KasperskyCode;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'code' => 'required|max:255|unique:kaspersky_codes,code,' . $this->id,
            ]
        );
        unset($rules['total_quantity']);
        return $rules;
    }
}
