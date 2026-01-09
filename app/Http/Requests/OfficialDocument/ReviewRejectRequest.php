<?php

namespace App\Http\Requests\OfficialDocument;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRejectRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'status' => 'pending_review',
        ]);
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending_review',
            'reviewer_id' => 'required|exists:users,id',
        ];
    }
}
