<?php

namespace App\Http\Requests\Airport;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
        );
    }
}
