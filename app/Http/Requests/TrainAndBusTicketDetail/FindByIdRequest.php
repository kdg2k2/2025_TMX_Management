<?php

namespace App\Http\Requests\TrainAndBusTicketDetail;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:train_and_bus_ticket_details,id'
        ];
    }
}
