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
            'inspection_expired_at' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'liability_insurance_expired_at' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'body_insurance_expired_at' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'status' => 'required|in:ready,unwashed,broken,faulty,lost,loaned',
        ];
    }
}
