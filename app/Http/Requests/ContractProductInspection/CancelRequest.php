<?php

namespace App\Http\Requests\ContractProductInspection;

use App\Http\Requests\BaseRequest;

class CancelRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
            'status' => 'cancel',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            app(FindByIdRequest::class)->rules(),
            [
                'created_by' => 'required|exists:users,id',
                'status' => 'required|in:cancel',
            ]
        );
    }
}
