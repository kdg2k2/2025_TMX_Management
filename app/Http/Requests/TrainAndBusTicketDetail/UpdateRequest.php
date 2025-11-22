<?php

namespace App\Http\Requests\TrainAndBusTicketDetail;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            app(FindByIdRequest::class)->rules(),
            [
                'train_and_bus_ticket_id' => 'required|exists:train_and_bus_tickets,id',
                'user_type' => 'required|in:internal,external',
                'user_id' => 'required_if:user_type,internal|exists:users,id',
                'external_user_name' => 'required_if:user_type,external|max:255',
                'departure_date' => 'nullable|date_format:Y-m-d',
                'return_date' => 'nullable|date_format:Y-m-d',
                'departure_place' => 'nullable|max:255',
                'return_place' => 'nullable|max:255',
                'train_number' => 'nullable|max:255',
                'ticket_price' => 'nullable|integer|min:0',
                'ticket_image_path' => 'nullable|file|mimes:png,jpg,jpeg,pdf',
                'note' => 'nullable|max:255',
                'created_by' => 'required|exists:users,id',
            ]
        );
    }
}
