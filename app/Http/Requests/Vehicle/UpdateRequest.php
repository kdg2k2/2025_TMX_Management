<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'license_plate' => 'required|max:255|unique:vehicles,license_plate,' . $this->id,
            ]
        );
    }
}
