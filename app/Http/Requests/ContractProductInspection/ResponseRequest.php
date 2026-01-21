<?php

namespace App\Http\Requests\ContractProductInspection;

use App\Http\Requests\BaseRequest;

class ResponseRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'inspector_user_id' => $this->user()->id,
            'status' => 'responded',
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            app(FindByIdRequest::class)->rules(),
            [
                'inspector_user_id' => 'required|exists:users,id',
                'status' => 'required|in:responded',
                'inspector_comment' => 'required|max:255',
                'inspector_comment_file_path' => 'nullable|file|mimes:docx,xlsx,rar,zip|max:10240',
            ]
        );
    }
}
