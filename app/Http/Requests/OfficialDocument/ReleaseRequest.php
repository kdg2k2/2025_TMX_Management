<?php

namespace App\Http\Requests\OfficialDocument;

class ReleaseRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'status' => 'released',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'status' => 'required|in:released',
                'released_date' => 'required|date_format:Y-d-m',
                'document_number' => 'required|max:255',
                'released_pdf_file' => 'required|file|mimes:pdf|max:51200',
                'signed_by' => 'nullable|exists:users,id',
            ]
        );
    }
}
