<?php

namespace App\Http\Requests\OfficialDocument;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'release_type' => 'nullable|in:new,revision,reply',
                'program_type' => 'nullable|in:contract,incoming,orther',
                'status' => 'nullable|in:pending_review,reviewed,approved,rejected,released',
            ]
        );
    }
}
