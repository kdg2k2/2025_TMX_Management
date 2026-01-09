<?php

namespace App\Http\Requests\OfficialDocument;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
            'creater_position' => $this->user()->position_id,
            'status' => $this->user()->position_id < 5 ? 'approved' : 'pending_review',  // nếu chức vụ là trưởng phó thì trạng thái sẽ là chờ phát hành
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'creater_position' => 'required|exists:positions,id',
            'official_document_type_id' => 'required|exists:official_document_types,id',
            'official_document_sector_id' => 'required_if:status,approved|exists:official_document_sectors,id',
            'program_type' => 'required|in:contract,incoming,orther',
            'contract_id' => 'nullable|required_if:program_type,contract|exists:contracts,id',
            'incoming_official_document_id' => 'nullable|required_if:program_type,incoming|exists:incoming_official_documents,id',
            'other_program_name' => 'nullable|required_if:program_type,orther|max:255',
            'release_type' => 'required|in:new,revision,reply',
            'status' => 'required|in:pending_review,approved',
            'name' => 'required|max:255',
            'reviewed_by' => 'required_if:status,pending_review|exists:users,id',
            'signed_by' => 'required|exists:users,id',
            'expected_release_date' => 'required|date_format:Y-m-d',
            'receiver_organization' => 'required|max:255',
            'receiver_address' => 'required|max:255',
            'receiver_name' => 'required|max:255',
            'receiver_phone' => 'required|max:255',
            'note' => 'nullable|max:255',
            'pending_review_docx_file' => 'required|file|mimes:docx|max:51200',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ];
    }
}
