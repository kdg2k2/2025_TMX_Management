<?php

namespace App\Http\Requests\PersonnelFile;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:personnel_files,id',
        ];
    }
}
