<?php

namespace App\Http\Requests\DeviceFix;

class FixedRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'fixed_at' => date('Y-m-d H:i:s'),
            'status' => 'fixed',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'fixed_at' => 'required|date_format:Y-m-d H:i:s',
                'status' => 'required|in:fixed',
            ]
        );
    }
}
