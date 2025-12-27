<?php

namespace App\Http\Requests\DeviceLoan;

class ReturnRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'returned_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'returned_at' => 'required|date_format:Y-m-d H:i:s',
            ]
        );
    }
}
