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
                'departure_date' => 'nullable|date_format:Y-m-d',
                'return_date' => 'nullable|date_format:Y-m-d|after:departure_date',
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
