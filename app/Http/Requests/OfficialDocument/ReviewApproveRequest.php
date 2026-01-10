<?php

namespace App\Http\Requests\OfficialDocument;

class ReviewApproveRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'status' => 'reviewed',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'status' => 'required|in:reviewed',
                'official_document_sector_id' => 'required|exists:official_document_sectors,id',
                'revision_docx_file' => 'nullable|file|mimes:docx|max:51200',
            ]
        );
    }
}
