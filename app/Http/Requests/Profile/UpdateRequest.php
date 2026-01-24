<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|max:255',
            'phone' => 'nullable|unique:users,phone,' . $userId,
            'citizen_identification_number' => 'nullable|unique:users,citizen_identification_number,' . $userId,
            'date_of_birth' => 'nullable|date_format:Y-m-d',
            'address' => 'nullable|max:255',
            'path' => 'nullable|file|mimes:png,jpg,jpeg,webp',
            'path_signature' => 'nullable|file|mimes:png,jpg,jpeg,webp',
        ];
    }
}
