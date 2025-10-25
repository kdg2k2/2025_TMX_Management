<?php

namespace App\Http\Requests\GoogleDrive;

use App\Http\Requests\BaseRequest;

class UpdateFolderRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'folder_id' => 'required|string',
            'new_name' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'folder_id.required' => 'ID folder không được để trống',
            'new_name.required' => 'Tên mới không được để trống',
            'new_name.max' => 'Tên mới không được vượt quá 255 ký tự',
        ];
    }
}
