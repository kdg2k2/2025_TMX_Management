<?php

namespace App\Http\Requests\TrainAndBusTicketDetail;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'train_and_bus_ticket_id' => 'nullable|exists:train_and_bus_tickets,id',
            ]
        );
    }
}
