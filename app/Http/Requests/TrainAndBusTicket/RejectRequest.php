<?php

namespace App\Http\Requests\TrainAndBusTicket;

use App\Traits\HasApprovalData;

class RejectRequest extends FindByIdRequest
{
    use HasApprovalData;

    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->mergeApprovalData();
        $this->merge([
            'status' => 'rejected',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            $this->getApprovalRules(),
            [
                'status' => 'required|in:rejected',
                'rejection_note' => 'required|max:255',
            ]
        );
    }
}
