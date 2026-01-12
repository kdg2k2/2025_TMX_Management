<?php

namespace App\Http\Requests\KasperskyCodeRegistration;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'type' => 'nullable|in:personal,company,both',
                'status' => 'nullable|in:pending,approved,rejected',
                'created_by'=>'nullable|exists:users,id',
            ]
        );
    }
}
