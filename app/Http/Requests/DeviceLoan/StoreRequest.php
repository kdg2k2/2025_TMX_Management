<?php

namespace App\Http\Requests\DeviceLoan;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'borrowed_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'details' => 'required|array|min:1',
            'details.*.device_id' => 'required|exists:devices,id|distinct',
            'details.*.expected_return_at' => 'required|date_format:Y-m-d|after_or_equal:borrowed_date',
            'details.*.use_location' => 'required|max:255',
            'details.*.note' => 'nullable|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'created_by.required' => 'Người mượn không được để trống.',
            'created_by.exists'   => 'Người mượn không tồn tại.',

            'borrowed_date.required' => 'Ngày mượn không được để trống.',
            'borrowed_date.date_format' => 'Ngày mượn không đúng định dạng (Y-m-d).',
            'borrowed_date.after_or_equal' => 'Ngày mượn phải từ hôm nay trở đi.',

            'details.required' => 'Danh sách thiết bị mượn không được để trống.',
            'details.array'    => 'Danh sách thiết bị mượn không hợp lệ.',
            'details.min'      => 'Phải chọn ít nhất 1 thiết bị để mượn.',

            'details.*.device_id.required' => 'Thiết bị mượn không được để trống.',
            'details.*.device_id.distinct' => 'Một thiết bị không được chọn nhiều lần trong cùng phiếu mượn.',
            'details.*.device_id.exists'   => 'Thiết bị mượn không tồn tại.',

            'details.*.expected_return_at.required' => 'Ngày dự kiến trả không được để trống.',
            'details.*.expected_return_at.date_format' => 'Ngày dự kiến trả không đúng định dạng (Y-m-d).',
            'details.*.expected_return_at.after_or_equal' => 'Ngày dự kiến trả phải từ hôm nay trở đi.',

            'details.*.use_location.required' => 'Bắt buộc khai báo vị trí sử dụng thiết bị.',
            'details.*.use_location.max' => 'Ghi chú không được vượt quá 255 ký tự.',
            'details.*.note.max' => 'Ghi chú không được vượt quá 255 ký tự.',
        ];
    }
}
