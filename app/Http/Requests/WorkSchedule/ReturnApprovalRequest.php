<?php

namespace App\Http\Requests\WorkSchedule;

class ReturnApprovalRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'return_approval_date' => date('Y-m-d'),
            'return_approved_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'return_approval_status' => 'required|in:approved,rejected',
                'return_approval_note' => 'required|max:255',
                'return_approval_date' => 'required|date_format:Y-m-d',
                'return_approved_by' => 'required|exists:users,id',
            ]
        );
    }
}
