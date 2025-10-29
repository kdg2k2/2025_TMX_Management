<?php

namespace App\Http\Requests\LeaveRequest;

class AdjustApprovalRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'adjust_approval_date' => date('Y-m-d'),
            'adjust_approved_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'adjust_approval_status' => 'required|in:approved,rejected',
                'adjust_approval_note' => 'required|max:255',
                'adjust_approval_date' => 'required|date_format:Y-m-d',
                'adjust_approved_by' => 'required|exists:users,id',
            ]
        );
    }
}
