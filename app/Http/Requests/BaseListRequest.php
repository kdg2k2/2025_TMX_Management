<?php

namespace App\Http\Requests;

class BaseListRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'order_by' => $this->order_by ?? 'id',
            'sort_by' => $this->sort_by ? in_array($this->sort_by, ['desc', 'asc']) ? $this->sort_by : 'desc' : 'desc',
            'paginate' => $this->paginate ?? '0',
            'per_page' => $this->per_page ?? null,
            'page' => $this->page ?? null,
            'search' => $this->search ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'order_by' => 'required|string',
            'sort_by' => 'required|in:desc,asc',
            'paginate' => 'required|in:0,1',
            'per_page' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'search' => 'nullable|string',
        ];
    }
}
