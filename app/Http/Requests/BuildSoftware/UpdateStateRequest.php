<?php

namespace App\Http\Requests\BuildSoftware;

class UpdateStateRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'state' => 'required|in:pending,doing_business_analysis,construction_planning,in_progress,completed',
            ]
        );
    }
}
