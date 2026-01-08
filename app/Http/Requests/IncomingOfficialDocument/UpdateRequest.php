<?php

namespace App\Http\Requests\IncomingOfficialDocument;

class UpdateRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'attachment_file' => 'nullable|file|mimes:pdf|max:5120',
            ]
        );
    }
}
