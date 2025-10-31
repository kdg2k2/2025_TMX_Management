<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'is_banned' => $this->boolean('is_banned', false),
            'is_retired' => $this->boolean('is_retired', false),
            'is_salary_counted' => $this->boolean('is_salary_counted', true),
            'is_permanent' => $this->boolean('is_permanent', true),
            'is_childcare_mode' => $this->boolean('is_childcare_mode', false),
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
            'is_retired' => 'required|boolean',
            'is_salary_counted' => 'required|boolean',
            'is_permanent' => 'required|boolean',
            'is_childcare_mode' => 'required|boolean',
            'date_of_birth' => 'nullable|date_format:Y-m-d',
            'address' => 'nullable|max:255',
            'salary_level' => 'nullable|integer|min:0',
            'violation_penalty' => 'nullable|integer|min:0',
            'allowance_contact' => 'nullable|integer|min:0',
            'allowance_position' => 'nullable|integer|min:0',
            'allowance_fuel' => 'nullable|integer|min:0',
            'allowance_transport' => 'nullable|integer|min:0',
            'work_start_date' => 'nullable|date_format:Y-m-d',
            'work_end_date' => 'nullable|date_format:Y-m-d',
        ];
    }
}
