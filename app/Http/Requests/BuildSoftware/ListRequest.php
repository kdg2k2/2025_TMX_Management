<?php

namespace App\Http\Requests\BuildSoftware;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'development_case' => 'nullable|in:update,new,suddenly',
                'state' => 'nullable|in:pending,doing_business_analysis,construction_planning,in_progress,completed',
                'status' => 'nullable|in:pending,accepted,rejected',
            ]
        );
    }
}
