<?php

namespace App\Http\Requests\KasperskyCodeRegistration;

use App\Http\Requests\BaseApproveRequest;
use App\Models\KasperskyCode;
use App\Models\KasperskyCodeRegistration;
use App\Services\KasperskyCodeRegistrationService;
use App\Services\KasperskyCodeService;
use App\Traits\FormatDateTraits;

class ApproveRequest extends BaseApproveRequest
{
    use FormatDateTraits;

    public function rules(): array
    {
        $kasperskyCodeService = app(KasperskyCodeService::class);
        $kasperskyCodeRegistrationService = app(KasperskyCodeRegistrationService::class);
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'codes' => [
                    'required',
                    'array',
                    'min:1',
                    'max:2',
                    function ($attribute, $value, $fail) use ($kasperskyCodeService, $kasperskyCodeRegistrationService) {
                        $registration = $kasperskyCodeRegistrationService->findById($this->id, false, true);
                        if (!$registration)
                            return;

                        // Tính số lượt cần cấp
                        $requiredSlots = $registration['type']['original'] === 'both' ? 2 : 1;

                        $codes = $kasperskyCodeService->findByKeys($value, 'id');

                        // Nếu chọn 1 mã thì phải còn đủ lượt
                        if (count($value) === 1) {
                            $code = $codes->first();

                            if ($code['available_quantity'] < $requiredSlots)
                                $fail("Mã {$this->formatDateTimeForPreview($code['created_at'])} chỉ còn {$code['available_quantity']} lượt, không đủ {$requiredSlots} lượt cần thiết.");
                        }

                        // Nếu chọn 2 mã với type != 'both' thì không hợp lệ
                        if (count($value) === 2 && $registration['type']['original'] !== 'both')
                            $fail("Kiểu đăng ký '{$registration['type']['converted']}' chỉ cần 1 lượt, không cần chọn 2 mã.");
                    }
                ],
                'codes.*' => [
                    'exists:kaspersky_codes,id',
                    function ($attribute, $value, $fail) use ($kasperskyCodeService) {
                        $code = $kasperskyCodeService->findById($value);
                        if (!$code)
                            return;

                        // Check đã hết hạn
                        if ($code['is_expired'])
                            $fail("Mã {$this->formatDateTimeForPreview($code['created_at'])} đã hết hạn sử dụng.");

                        // Check đã hết lượt
                        if ($code['is_quantity_exceeded'] || $code['available_quantity'] <= 0)
                            $fail("Mã {$this->formatDateTimeForPreview($code['created_at'])} đã hết lượt sử dụng.");
                    }
                ],
            ]
        );
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            [
                'codes' => 'Mã kaspersky'
            ]
        );
    }
}
