<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'is_banned' => $this->boolean('is_banned'),
            'retired' => $this->boolean('retired'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|max:255',
            'phone' => 'nullable|unique:users,phone',
            'citizen_identification_number' => 'nullable|unique:users,citizen_identification_number',
            'path' => 'nullable|file|mimes:png,jpg,jpeg,webp',
            'path_signature' => 'nullable|file|mimes:png,jpg,jpeg,webp',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'job_title_id' => 'required|exists:job_titles,id',
            'role_id' => 'nullable|exists:roles,id',
            'is_banned' => 'required|boolean',
            'retired' => 'required|boolean',
        ];
    }
}
