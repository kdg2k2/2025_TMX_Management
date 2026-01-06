<?php

namespace App\Http\Requests;

use App\Traits\HasApprovalData;

class BaseApproveRequest extends BaseRequest
{
    use HasApprovalData;

    public function prepareForValidation()
    {
        $this->mergeApprovalData();
        $this->merge([
            'status' => 'approved',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            $this->getApprovalRules(),
            [
                'status' => 'required|in:approved',
                'approval_note' => 'required|max:255',
            ]
        );
    }
}
