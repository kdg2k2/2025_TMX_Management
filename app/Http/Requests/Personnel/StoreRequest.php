<?php

namespace App\Http\Requests\Personnel;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'name' => 'required|max:255|unique:personnels,name',
            'personnel_unit_id' => 'required|exists:personnel_units,id',
            'educational_level' => 'required|max:255',
        ];
    }
}
