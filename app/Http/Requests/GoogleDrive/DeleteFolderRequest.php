<?php

namespace App\Http\Requests\GoogleDrive;

use App\Http\Requests\BaseRequest;

class DeleteFolderRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'folder_id' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'folder_id.required' => 'ID folder không được để trống',
        ];
    }
}
