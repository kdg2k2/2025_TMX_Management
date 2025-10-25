<?php

namespace App\Http\Requests\GoogleDrive;

use App\Http\Requests\BaseRequest;

class CreateFolderRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'folder_name' => 'required|string|max:255',
            'parent_id' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'folder_name.required' => 'Tên folder không được để trống',
            'folder_name.max' => 'Tên folder không được vượt quá 255 ký tự',
        ];
    }
}
