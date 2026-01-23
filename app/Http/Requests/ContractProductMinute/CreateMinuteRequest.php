<?php

namespace App\Http\Requests\ContractProductMinute;

use App\Http\Requests\BaseRequest;

class CreateMinuteRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'contract_id' => 'required|exists:contracts,id',
            'handover_date' => 'nullable|date_format:Y-m-d',
            'legal_basis' => 'nullable|max:500',
            'handover_content' => 'nullable|max:500',
            'professional_user_id' => 'required|exists:users,id',
            'disbursement_user_id' => 'required|exists:users,id',
        ];
    }
}
