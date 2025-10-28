<?php

namespace App\Http\Requests\WorkSchedule;

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
            'address' => 'required|max:255',
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d',
            'content' => 'required|max:255',
            'type_program' => 'required|in:contract,other',
            'contract_id' => $this->type_program == 'contract' ? 'required' : 'nullable' . '|exists:contracts,id',
            'other_program' => $this->type_program == 'other' ? 'required' : 'nullable' . '|max:255',
            'clue' => 'nullable|max:255',
            'participants' => 'nullable|max:255',
            'note' => 'nullable|max:255',
            'total_trip_days' => 'required|integer|min:1',
        ];
    }
}
