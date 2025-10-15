<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFindByIdRequest;
use Illuminate\Foundation\Http\FormRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function prepareForValidation()
    {
        $this->merge(parent::prepareForValidation());
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:users,id',
        ];
    }
}
