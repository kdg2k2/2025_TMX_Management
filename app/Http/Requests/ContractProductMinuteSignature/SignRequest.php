<?php

namespace App\Http\Requests\ContractProductMinuteSignature;

use Illuminate\Foundation\Http\FormRequest;

class SignRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'minute_id' => $this->input('minute_id') ?? request()->query('minute_id'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minute_id' => 'required|exists:contract_product_minutes,id',
            'signature_type' => 'required|in:draw,upload,profile',
            'signature_data' => 'required_if:signature_type,draw|string',
            'signature_file' => 'required_if:signature_type,upload|file|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'minute_id.required' => 'ID biên bản là bắt buộc',
            'minute_id.exists' => 'Biên bản không tồn tại',
            'signature_type.required' => 'Vui lòng chọn phương thức ký',
            'signature_type.in' => 'Phương thức ký không hợp lệ',
            'signature_data.required_if' => 'Vui lòng vẽ chữ ký',
            'signature_file.required_if' => 'Vui lòng chọn file ảnh chữ ký',
            'signature_file.mimes' => 'File phải là ảnh PNG hoặc JPG',
            'signature_file.max' => 'File không được vượt quá 2MB',
        ];
    }
}
