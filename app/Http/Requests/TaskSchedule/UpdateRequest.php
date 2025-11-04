<?php

namespace App\Http\Requests\TaskSchedule;

use Carbon\Carbon;
use Cron\CronExpression;

class UpdateRequest extends FindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
        // Parse next_run_at từ datetime-local input
        if ($this->has('next_run_at') && $this->next_run_at) {
            try {
                // Input từ FE: "2025-11-07T09:00" (datetime-local format)
                // Convert sang Carbon với timezone của server
                $this->merge([
                    'next_run_at' => Carbon::parse($this->next_run_at)->format('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                // Nếu parse fail, để null để validation bắt lỗi
                $this->merge(['next_run_at' => null]);
            }
        }

        // Convert is_active sang boolean
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'frequency' => 'required|in:daily,weekly,monthly',
                'cron_expression' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        try {
                            new CronExpression($value);
                        } catch (\Exception $e) {
                            $fail('Cron expression không hợp lệ. VD: 0 0 * * *');
                        }
                    }
                ],
                'users' => 'required|array|min:1',
                'users.*' => 'exists:users,id',
                'next_run_at' => [
                    'nullable',
                    'date',
                    // 'after:now',  // Đảm bảo là thời gian tương lai
                ],
                'is_active' => 'required|boolean',
            ],
        );
    }
}
