<?php

namespace App\Http\Requests\VehicleLoan;

use Carbon\Carbon;
use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
            'vehicle_pickup_time' => Carbon::parse($this->vehicle_pickup_time)->format('Y-m-d H:i'),
        ]);
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'created_by' => 'required|exists:users,id',
            'destination' => 'required|max:255',
            'work_content' => 'required|max:255',
            'vehicle_pickup_time' => 'required|date_format:Y-m-d H:i',
            'estimated_vehicle_return_date' => 'required|date_format:Y-m-d',
            'before_front_image' => 'required|file|mimes:png,jpg,jpeg',
            'before_rear_image' => 'required|file|mimes:png,jpg,jpeg',
            'before_left_image' => 'required|file|mimes:png,jpg,jpeg',
            'before_right_image' => 'required|file|mimes:png,jpg,jpeg',
            'before_odometer_image' => 'required|file|mimes:png,jpg,jpeg',
            'current_km' => 'required|integer',
        ];
    }
}
