<?php

namespace App\Http\Requests\EmploymentContractPersonnel;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'personnel_unit_id' => 'nullable|exists:personnel_units,id',
            ]
        );
    }
}
