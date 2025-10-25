<?php

namespace App\Http\Requests\GoogleDrive;

use App\Http\Requests\BaseRequest;

class ListFoldersRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'parent_id' => 'nullable|string'
        ];
    }
}
