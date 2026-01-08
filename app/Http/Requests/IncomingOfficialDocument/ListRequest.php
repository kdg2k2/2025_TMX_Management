<?php

namespace App\Http\Requests\IncomingOfficialDocument;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(), [
                'status' => 'nullable|in:new,in_progress,completed',
                'program_type' => 'nullable|in:contract,orther',
                'official_document_type_id' => 'nullable|exists:official_document_types,id',
            ]
        );
    }
}
