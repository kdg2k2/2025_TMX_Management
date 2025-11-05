<?php

namespace App\Http\Requests\BoardMeetingMinute;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:internal_meeting_minutes,id'
        ];
    }
}
