<?php

namespace App\Http\Requests\InternalMeetingMinute;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
        );
    }
}
