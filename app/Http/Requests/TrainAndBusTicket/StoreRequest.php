<?php

namespace App\Http\Requests\TrainAndBusTicket;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:contract,other',
            'contract_id' => 'required_if:type,contract|exists:contracts,id',
            'other_program_name' => 'required_if:type,other|max:255',
            'estimated_travel_time' => 'required|date_format:Y-m-d',
            'expected_departure' => 'required|max:255',
            'expected_destination' => 'required|max:255',
            'created_by' => 'required|exists:users,id',
            'details' => 'required|array|min:1',
            'details.*.user_type' => 'required|in:internal,external',
            'details.*.user_id' => 'nullable|exists:users,id',
            'details.*.external_user_name' => 'nullable|max:255',
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
