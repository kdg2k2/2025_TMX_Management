<?php

namespace App\Http\Requests\ContractPersonnel;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'is_in_contract' => isset($this->is_in_contract) ? $this->boolean('is_in_contract') : null,
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'contract_id' => 'nullable|exists:contracts,id',
                'personnel_id' => 'nullable|exists:personnels,id',
                'is_in_contract' => 'nullable|boolean',
            ]
        );
    }
}
