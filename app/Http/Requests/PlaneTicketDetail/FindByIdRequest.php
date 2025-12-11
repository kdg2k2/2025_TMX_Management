<?php

namespace App\Http\Requests\PlaneTicketDetail;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:plane_ticket_details,id'
        ];
    }
}
