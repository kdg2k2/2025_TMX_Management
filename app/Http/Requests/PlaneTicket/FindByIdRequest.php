<?php

namespace App\Http\Requests\PlaneTicket;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:plane_tickets,id'
        ];
    }
}
