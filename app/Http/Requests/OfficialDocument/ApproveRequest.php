<?php

namespace App\Http\Requests\OfficialDocument;

use App\Http\Requests\BaseApproveRequest;

class ApproveRequest extends BaseApproveRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'released_pdf_file' => 'required|file|mimes:pdf|max:51200',
            ]
        );
    }
}
