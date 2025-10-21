<?php

namespace App\Http\Requests;

class BaseFindByIdRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->query('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
