<?php

namespace App\Http\Requests\InternalBulletin;

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
