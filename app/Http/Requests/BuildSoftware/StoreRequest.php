<?php

namespace App\Http\Requests\BuildSoftware;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
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
            'contract_id' => 'nullable|exists:contracts,id',
            'created_by' => 'required|exists:users,id',
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
            'attachment' => 'nullable|file|mimes:docx,pdf,rar,zip|max:10240',
            'development_case' => 'required|in:update,new,suddenly',
            'deadline' => 'required|after_or_equal:today|date_format:Y-m-d',
            'business_analysts' => 'required|array',
            'business_analysts.*' => 'required|exists:users,id',
            'members' => 'required|array',
            'members.*' => 'required|exists:users,id',
        ];
    }
}
