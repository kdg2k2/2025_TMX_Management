<?php

namespace App\Http\Requests\IncomingOfficialDocument;

use App\Http\Requests\BaseRequest;

class CompleteRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'status' => 'completed',
        ]);
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:completed',
        ];
    }
}
