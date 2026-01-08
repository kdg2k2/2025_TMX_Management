<?php

namespace App\Http\Requests\IncomingOfficialDocument;

use App\Http\Requests\BaseRequest;

class AssignRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'status' => 'in_progress',
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:in_progress',
            'created_by' => 'required|exists:users,id',
            'task_assignee_id' => 'required|exists:users,id',
            'task_completion_deadline' => 'required|date_format:Y-m-d',
            'task_notes' => 'nullable|max:255',
        ];
    }
}
