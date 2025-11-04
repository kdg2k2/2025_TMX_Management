<?php

namespace App\Http\Requests\TaskSchedule;

use Cron\CronExpression;

class UpdateRequest extends FindByIdRequest
{
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
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
            ]
        );
    }
}
