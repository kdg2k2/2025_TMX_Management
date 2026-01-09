<?php

namespace App\Http\Requests\OfficialDocument;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'status' => 'released',
        ]);
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:released',
            'official_document_sector_id' => 'required|exists:official_document_sectors,id',
            'revision_docx_file' => 'nullable|file|mimes:docx|max:51200',
        ];
    }
}
