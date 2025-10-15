<?php

namespace App\Http\Requests\ContractAppendix;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'contract_id' => 'required|exists:contracts,id',
            'content' => 'nullable|max:255',
            'renewal_date' => 'required|date_format:Y-m-d',
            'renewal_end_date' => 'required|date_format:Y-m-d',
            'renewal_letter' => 'nullable|file|mimes:doc,docx,pdf,rar,zip',
            'renewal_approval_letter' => 'nullable|file|mimes:doc,docx,pdf,rar,zip',
            'renewal_appendix' => 'nullable|file|mimes:doc,docx,pdf,rar,zip',
            'other_documents' => 'nullable|file|mimes:doc,docx,pdf,rar,zip',
            'adjusted_value' => 'nullable|numeric|min:0',
            'note' => 'nullable|max:255',
        ];
    }
}
