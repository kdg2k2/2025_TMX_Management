<?php

namespace App\Http\Requests\BuildSoftware;

use App\Http\Requests\BaseRequest;

class VerifyRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'verify_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'verify_by' => 'required|exists:users,id',
            'id' => app(FindByIdRequest::class)->rules()['id'],
        ];
    }
}
