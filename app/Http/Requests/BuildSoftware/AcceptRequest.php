<?php

namespace App\Http\Requests\BuildSoftware;

class AcceptRequest extends VerifyRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'accepted_at' => date('Y-m-d H:i:s'),
            'status' => 'accepted',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'accepted_at' => 'required|date_format:Y-m-d H:i:s',
                'status' => 'required|in:accepted',
            ]
        );
    }
}
