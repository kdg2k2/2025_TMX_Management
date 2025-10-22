<?php

namespace App\Http\Requests\PersonnelCustomField;

use App\Http\Requests\BaseRequest;
use App\Services\StringHandlerService;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
            'field' => app(StringHandlerService::class)->createSlug($this->name ?? '', '_'),
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'z_index' => 'required|integer',
            'name' => 'required|max:255|unique:personnel_custom_fields,name',
            'field' => 'required|max:255|unique:personnel_custom_fields,field',
            'type' => 'required|in:text,date,datetime-local,number',
        ];
    }
}
