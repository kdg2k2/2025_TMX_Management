<?php

namespace App\Http\Requests\Airline;

class UpdateRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'name' => 'required|max:255|unique:airlines,name,' . $this->id
            ],
        );
    }
}
