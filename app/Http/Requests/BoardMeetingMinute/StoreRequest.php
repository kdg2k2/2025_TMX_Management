<?php

namespace App\Http\Requests\BoardMeetingMinute;

use Carbon\Carbon;
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
            'meeting_day' => 'required|date_format:Y-m-d',
            'number' => 'required|integer',
            'main_content' => 'required|max:500',
            'path' => 'nullable|mimes:pdf',
        ];
    }
}
