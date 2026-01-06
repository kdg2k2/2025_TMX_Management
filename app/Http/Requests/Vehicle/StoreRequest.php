<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'brand' => 'required|max:255',
            'license_plate' => 'required|max:255|unique:vehicles,license_plate',
            'current_km' => 'required|integer|min:0',
            'maintenance_km' => 'nullable|integer|gte:current_km',
            'inspection_expired_at' => 'nullable|date_format:Y-m-d',
            'liability_insurance_expired_at' => 'nullable|date_format:Y-m-d',
            'body_insurance_expired_at' => 'nullable|date_format:Y-m-d',
            'status' => 'required|in:ready,unwashed,broken,faulty,lost,loaned',
        ];
    }
}
