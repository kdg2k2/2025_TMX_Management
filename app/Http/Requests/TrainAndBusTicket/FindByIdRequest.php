<?php

namespace App\Http\Requests\TrainAndBusTicket;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:train_and_bus_tickets,id'
        ];
    }
}
