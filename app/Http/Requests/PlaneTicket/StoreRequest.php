<?php

namespace App\Http\Requests\PlaneTicket;

use Carbon\Carbon;
use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
            'estimated_flight_time' => Carbon::parse($this->estimated_flight_time)->format('Y-m-d H:i'),
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:contract,other',
            'contract_id' => 'required_if:type,contract|nullable|exists:contracts,id',
            'other_program_name' => 'required_if:type,other|nullable|max:255',
            'estimated_flight_time' => 'required|date_format:Y-m-d H:i|after_or_equal:today',
            'airport_id' => 'required|exists:airports,id',
            'airline_id' => 'required|exists:airlines,id',
            'plane_ticket_class_id' => 'required|exists:plane_ticket_classes,id',
            'checked_baggage_allowances' => 'required|integer|min:0',
            'created_by' => 'required|exists:users,id',
            'details' => 'required|array|min:1',
            'details.*.user_type' => 'required|in:internal,external',
            'details.*.user_id' => 'nullable|exists:users,id|distinct',
            'details.*.external_user_name' => 'nullable|max:255|distinct',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->details ?? [] as $index => $detail) {
                if ($detail['user_type'] === 'internal' && empty($detail['user_id'])) {
                    $validator->errors()->add(
                        "details.$index.user_id",
                        'User ID không được bỏ trống'
                    );
                }
                if ($detail['user_type'] === 'external' && empty($detail['external_user_name'])) {
                    $validator->errors()->add(
                        "details.$index.external_user_name",
                        'Tên người dùng không được bỏ trống'
                    );
                }
            }
        });
    }
}
