<?php

namespace App\Http\Requests\PlaneTicketClass;

class UpdateRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'name' => 'required|max:255|unique:plane_ticket_classes,name,' . $this->id
            ],
        );
    }
}
