<?php

namespace App\Http\Requests\OfficialDocument;

class ReviewRejectRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'status' => 'pending_review',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'status' => 'required|in:pending_review',
                'reviewed_by' => 'required|exists:users,id',
            ]
        );
    }
}
