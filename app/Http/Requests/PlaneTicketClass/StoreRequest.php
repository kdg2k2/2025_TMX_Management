<?php

namespace App\Http\Requests\PlaneTicketClass;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:plane_ticket_classes,name'
        ];
    }
}
