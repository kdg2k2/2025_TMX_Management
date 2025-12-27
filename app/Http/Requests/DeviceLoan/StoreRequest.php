<?php

namespace App\Http\Requests\DeviceLoan;

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
            'device_id' => 'required|exists:devices,id',
            'created_by' => 'required|exists:users,id',
            'borrowed_date' => 'required|date_format:Y-m-d',
            'expected_return_at' => 'required|date_format:Y-m-d',
            'note' => 'nullable|max:255',
        ];
    }
}
