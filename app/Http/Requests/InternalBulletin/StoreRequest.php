<?php

namespace App\Http\Requests\InternalBulletin;

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
            'main_content' => 'required|max:500',
            'path' => 'required|mimes:pdf',
        ];
    }
}
