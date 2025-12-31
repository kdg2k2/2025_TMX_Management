<?php

namespace App\Http\Requests\VehicleLoan;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:vehicle_loans,id',
        ];
    }
}
