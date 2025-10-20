<?php

namespace App\Http\Requests\BuildSoftware;

class RejectRequest extends VerifyRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'rejected_at' => date('Y-m-d H:i:s'),
            'status' => 'rejected',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'rejected_at' => 'required|date_format:Y-m-d H:i:s',
                'rejection_reason' => 'required|max:255',
                'status' => 'required|in:rejected',
            ]
        );
    }
}
