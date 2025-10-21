<?php

namespace App\Http\Requests\PersonnelUnit;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'name' => 'required|max:255|unique:personnel_units,name,' . $this->id,
                'short_name' => 'required|max:255|unique:personnel_units,short_name,' . $this->id,
            ]
        );
    }
}
