<?php

namespace App\Http\Requests\IncomingOfficialDocument;

class AssignRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'status' => 'in_progress',
            'assinged_by' => $this->user()->id,
            'assign_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'status' => 'required|in:in_progress',
                'assinged_by' => 'required|exists:users,id',
                'assign_at' => 'required|date_format:Y-m-d H:i:s',
                'task_assignee_id' => 'required|exists:users,id',
                'task_completion_deadline' => 'required|date_format:Y-m-d',
                'task_notes' => 'nullable|max:255',
                'users' => 'nullable|array|min:1',
                'users.*' => 'exists:users,id'
            ]
        );
    }
}
