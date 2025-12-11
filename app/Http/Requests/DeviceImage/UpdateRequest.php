<?php

namespace App\Http\Requests\DeviceImage;

class UpdateRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'path' => 'file|mimes:png,jpg,jpeg,webp|max:5120',
            ]
        );
    }
}
