<?php

namespace App\Traits;

trait HasApprovalData
{
    protected function mergeApprovalData()
    {
        $this->merge([
            'approved_by' => $this->user()->id,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function getApprovalRules(): array
    {
        return [
            'approved_by' => 'required|exists:users,id',
            'approved_at' => 'required|date_format:Y-m-d H:i:s',
        ];
    }
}
