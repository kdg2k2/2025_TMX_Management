<?php

namespace App\Http\Requests\Unit;

class UpdateRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'name' => 'required|string|max:255|unique:units,name,' . $this->id,
            ]
        );
    }
}
