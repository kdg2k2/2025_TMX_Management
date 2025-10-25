<?php

namespace App\Http\Requests\GoogleDrive;

use App\Http\Requests\BaseRequest;

class CallbackRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'code' => 'required|string',
            'scope' => 'nullable|string',
            'state' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Không nhận được mã xác thực từ Google',
        ];
    }
}
