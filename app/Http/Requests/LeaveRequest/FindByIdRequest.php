<?php

namespace App\Http\Requests\LeaveRequest;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:table,leave_requests,id',
        ];
    }
}
