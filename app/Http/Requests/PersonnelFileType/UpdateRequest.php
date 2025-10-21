<?php

namespace App\Http\Requests\PersonnelFileType;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'name' => 'required|max:255|unique:personnel_file_types,name,' . $this->id,
            ],
        );
    }
}
