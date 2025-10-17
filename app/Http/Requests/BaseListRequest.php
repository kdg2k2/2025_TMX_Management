<?php

namespace App\Http\Requests;

class BaseListRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'load_relations' => $this->boolean('load_relations', true),
            'order_by' => $this->order_by ?? 'id',
            'sort_by' => $this->sort_by ? in_array($this->sort_by, ['desc', 'asc']) ? $this->sort_by : 'desc' : 'desc',
            'paginate' => $this->boolean('paginate', false),
            'per_page' => $this->per_page ?? 10,
            'page' => $this->page ?? 1,
            'search' => $this->search ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'load_relations' => 'required|boolean',
            'order_by' => 'required|string',
            'sort_by' => 'required|in:desc,asc',
            'paginate' => 'required|boolean',
            'per_page' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'search' => 'nullable|string',
        ];
    }
}
