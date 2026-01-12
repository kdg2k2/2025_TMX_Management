<?php

namespace App\Http\Requests\KasperskyCodeRegistration;

use App\Http\Requests\BaseRequest;
use App\Models\KasperskyCodeRegistration;
use App\Services\KasperskyCodeRegistrationService;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:personal,company,both',
            'created_by' => 'required|exists:users,id',
            'reason' => 'nullable|max:255',
            'device_id' => [
                'nullable',
                'required_unless:type,personal',
                'exists:devices,id',
                function ($attribute, $value, $fail) {
                    if (!$value)
                        return;

                    $existingRegistrations = app(KasperskyCodeRegistrationService::class)->registrationApprovedIsntExpired();
                    $hasValidCode = false;

                    foreach ($existingRegistrations as $registration)
                        if ($registration->codes->isNotEmpty())
                            $hasValidCode = true;

                    if ($hasValidCode)
                        $fail('Thiết bị này đã được cấp mã Kaspersky và còn hạn');
                }
            ],
        ];
    }
}
