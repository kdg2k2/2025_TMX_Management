<?php

namespace App\Http\Requests\GoogleDrive;

use App\Http\Requests\BaseRequest;

class SearchFolderRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'folder_name' => 'required|string|min:1'
        ];
    }

    public function messages()
    {
        return [
            'folder_name.required' => 'Từ khóa tìm kiếm không được để trống',
            'folder_name.min' => 'Từ khóa tìm kiếm phải có ít nhất 1 ký tự',
        ];
    }
}
