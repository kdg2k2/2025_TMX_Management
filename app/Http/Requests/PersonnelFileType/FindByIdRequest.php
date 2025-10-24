<?php

namespace App\Http\Requests\PersonnelFileType;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:personnel_file_types,id',
        ];
    }
}
