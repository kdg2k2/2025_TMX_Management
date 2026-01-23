<?php

namespace App\Http\Requests\ContractProductMinute;

class SignatureRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'issue_note' => 'nullable|string|max:1000',
            ]
        );
    }
}
