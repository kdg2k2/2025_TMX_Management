<?php

namespace App\Http\Requests\KasperskyCode;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'is_quantity_exceeded' => $this->is_quantity_exceeded ? $this->boolean('is_quantity_exceeded') : null,
            'is_expired' => $this->is_expired ? $this->boolean('is_expired') : null,
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'is_quantity_exceeded' => 'nullable|boolean',
                'is_expired' => 'nullable|boolean',
            ]
        );
    }
}
