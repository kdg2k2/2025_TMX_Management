<?php

namespace App\Http\Requests;

class BaseFindByIdRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        return [
            'id' => $this->query('id'),
        ];
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
