<?php

namespace App\Http\Requests\User;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = array_merge(
            parent::rules(),
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'password' => 'nullable|max:255',
                'email' => 'nullable|email|unique:users,email,' . $this->id,
                'phone' => 'nullable|unique:users,phone,' . $this->id,
                'citizen_identification_number' => 'nullable|unique:users,citizen_identification_number,' . $this->id,
            ]
        );
        if (!$this->password)
            unset($rules['password']);
        return $rules;
    }
}
