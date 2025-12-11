<?php

namespace App\Http\Requests\DeviceType;

class UpdateRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'name' => 'required|unique:device_types,name,' . $this->id,
            ]
        );
    }
}
