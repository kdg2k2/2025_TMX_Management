<?php

namespace App\Http\Requests\IncomingOfficialDocument;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules()
    {
        return [
            'created_by' => 'required|exists:users,id',
            'official_document_type_id' => 'required|exists:official_document_types,id',
            'program_type' => 'required|in:contract,orther',
            'contract_id' => 'required_if:program_type,contract',
            'other_program_name' => 'required_if:program_type,orther',
            'document_number' => 'required|max:255',
            'issuing_date' => 'nullable|date_format:Y-m-d',
            'received_date' => 'required|date_format:Y-m-d',
            'content_summary' => 'required|max:255',
            'sender_address' => 'nullable|max:255',
            'signer_name' => 'required|max:255',
            'signer_position' => 'required|max:255',
            'contact_person_name' => 'nullable|max:255',
            'contact_person_address' => 'nullable|max:255',
            'contact_person_phone' => 'nullable|max:255',
            'notes' => 'nullable|max:255',
            'attachment_file' => 'required|file|mimes:pdf|max:5120',
        ];
    }
}
