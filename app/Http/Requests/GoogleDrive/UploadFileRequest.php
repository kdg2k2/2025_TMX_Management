<?php

namespace App\Http\Requests\GoogleDrive;

use App\Http\Requests\BaseRequest;

class UploadFileRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'file' => 'required|file|max:102400',  // Max 100MB
            'folder_id' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Vui lòng chọn file để upload',
            'file.file' => 'File không hợp lệ',
            'file.max' => 'Kích thước file không được vượt quá 100MB',
        ];
    }
}
