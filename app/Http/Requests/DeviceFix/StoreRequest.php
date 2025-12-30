<?php

namespace App\Http\Requests\DeviceFix;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'details' => 'required|array|min:1',
            'details.*.device_id' => 'required|exists:devices,id|distinct',
            'details.*.suggested_content' => 'required|max:255',
            'details.*.device_status' => 'required|max:255',
            'details.*.note' => 'nullable|max:255',
        ];
    }
}
