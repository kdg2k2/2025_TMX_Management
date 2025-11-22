<?php

namespace App\Http\Requests\TrainAndBusTicket;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules()
        );
    }
}
