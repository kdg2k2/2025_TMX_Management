<?php

namespace App\Http\Requests\BiddingImplementationPersonnel;

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
            'created_by' => 'required|exists:users,id',
            'personnels' => 'required|array',
            'personnels.*.bidding_id' => 'required|exists:biddings,id',
            'personnels.*.personnel_id' => 'required|exists:personnels,id',
            'personnels.*.files' => 'required|array',
            'personnels.*.files.*' => 'required|exists:personnel_files,id',
            'personnels.*.job_title' => 'required|in:project_manager,topic_leader,expert,support_staff',
        ];
    }
}
