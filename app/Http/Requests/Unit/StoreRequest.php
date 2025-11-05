<?php

namespace App\Http\Requests\Unit;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules()
    {
        return [
            'created_by' => 'required|exists:users,id',
            'name' => 'required|max:255|unique:units,name',
            'province_code' => 'required|exists:provinces,code'
        ];
    }
}
