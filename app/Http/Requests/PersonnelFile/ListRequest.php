<?php

namespace App\Http\Requests\PersonnelFile;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'personnel_id' => 'nullable|exists:personnels,id',
            'type_id' => 'nullable|exists:personnel_file_types,id',
        ]);
    }
}
