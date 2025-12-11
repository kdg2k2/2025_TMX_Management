<?php

namespace App\Http\Requests\PlaneTicketDetail;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'plane_ticket_id' => 'nullable|exists:plane_tickets,id',
            ]
        );
    }
}
