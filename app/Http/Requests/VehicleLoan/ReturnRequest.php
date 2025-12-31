<?php

namespace App\Http\Requests\VehicleLoan;

class ReturnRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'returned_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'return_front_image' => 'required|file|mimes:png,jpg,jpeg',
                'return_rear_image' => 'required|file|mimes:png,jpg,jpeg',
                'return_left_image' => 'required|file|mimes:png,jpg,jpeg',
                'return_right_image' => 'required|file|mimes:png,jpg,jpeg',
                'return_odometer_image' => 'required|file|mimes:png,jpg,jpeg',
                'return_km' => 'required|integer|min:1',
                'returned_at' => 'required|date_format:Y-m-d H:i:s',
                'vehicle_status_return' => 'required|in:ready,unwashed,broken,faulty,lost',
                'fuel_cost' => 'nullable|integer|min:1',
                'fuel_cost_paid_by' => 'nullable|exists:users,id',
                'maintenance_cost' => 'nullable|integer|min:1',
                'maintenance_cost_paid_by' => 'nullable|exists:users,id',
            ]
        );
    }
}
