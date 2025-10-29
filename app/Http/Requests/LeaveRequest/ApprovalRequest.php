<?php

namespace App\Http\Requests\LeaveRequest;

class ApprovalRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'approval_date' => date('Y-m-d'),
            'approved_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'approval_status' => 'required|in:approved,rejected',
                'approval_note' => 'required|max:255',
                'approval_date' => 'required|date_format:Y-m-d',
                'approved_by' => 'required|exists:users,id',
            ]
        );
    }
}

