<?php

namespace App\Http\Requests\ContractProductMinute;

class ReplaceMinuteRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'file_docx' => 'required|file|mimes:docx',
            ]
        );
    }
}
