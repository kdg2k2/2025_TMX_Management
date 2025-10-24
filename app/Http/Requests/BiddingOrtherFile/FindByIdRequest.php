<?php

namespace App\Http\Requests\BiddingOrtherFile;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:bidding_orther_files,id',
        ];
    }
}
