<?php

namespace App\Http\Requests\VehicleLoan;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'status' => 'nullable|in:pending,approved,rejected,returned',
                'vehicle_status_return' => 'nullable|in:ready,unwashed,broken,faulty,lost',
                'vehicle_id' => 'nullable|exists:vehicles,id',
                'created_by' => 'nullable|exists:users,id',
            ]
        );
    }
}
