<?php

namespace App\Http\Requests;

class BaseListRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'paginate' => $this->paginate ?? '0',
            'per_page' => $this->per_page ?? null,
            'page' => $this->page ?? null,
            'search' => $this->search ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'paginate' => 'required|in:0,1',
            'per_page' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'search' => 'nullable|string',
        ];
    }
}
