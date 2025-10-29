<?php

namespace App\Http\Requests\WorkSchedule;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'is_completed' => $this->is_completed ? $this->boolean('is_completed') : null,
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'created_by' => 'nullable|exists:users,id',
                'type_program' => 'nullable|in:contract,other',
                'contract_id' => 'nullable|exists:contracts,id',
                'approval_status' => 'nullable|in:pending,approved,rejected',
                'return_approval_status' => 'nullable|in:none,pending,approved,rejected',
                'is_completed' => 'nullable|boolean',
                'from_date' => 'nullable|date_format:Y-m-d',
                'to_date' => 'nullable|date_format:Y-m-d',
            ]
        );
    }
}
