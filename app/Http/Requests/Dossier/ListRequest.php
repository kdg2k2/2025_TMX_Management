<?php

namespace App\Http\Requests\Dossier;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'contract_id' => 'nullable|integer|exists:contracts,id',
                'nam' => 'nullable|integer|exists:contracts,nam',
            ]
        );
    }
}
