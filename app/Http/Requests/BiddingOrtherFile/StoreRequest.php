<?php

namespace App\Http\Requests\BiddingOrtherFile;

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
            'orther_file' => 'required|array',
            'orther_file.*.bidding_id' => 'required|exists:biddings,id',
            'orther_file.*.content' => 'required|max:255',
            'orther_file.*.path' => 'required|file|mimes:pdf,xlsx,docx',
        ];
    }
}
