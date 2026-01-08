<?php

namespace App\Http\Requests\IncomingOfficialDocument;

class CompleteRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'status' => 'completed',
            'complete_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'status' => 'required|in:completed',
                'complete_at' => 'required|date_format:Y-m-d H:i:s',
            ]
        );
    }
}
