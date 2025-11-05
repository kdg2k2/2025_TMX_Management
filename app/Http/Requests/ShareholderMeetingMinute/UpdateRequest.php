<?php

namespace App\Http\Requests\ShareholderMeetingMinute;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'path' => 'nullable|mimes:pdf',
            ]
        );
    }
}
