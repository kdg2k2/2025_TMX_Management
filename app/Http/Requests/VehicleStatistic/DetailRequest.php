<?php

namespace App\Http\Requests\VehicleStatistic;

class DetailRequest extends DataRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'type' => 'required|string|in:vehicle_status,vehicle_loan,warning',
                'filter' => 'required|string',
            ]
        );
    }

    public function attributes(): array
    {
        return [
            'type' => 'loại thống kê',
            'filter' => 'bộ lọc',
            'year' => 'năm',
            'month' => 'tháng',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $filter = $this->input('filter');

            // Validate filter based on type
            if ($type === 'vehicle_status') {
                $validStatuses = ['ready', 'unwashed', 'broken', 'faulty', 'lost', 'loaned'];
                if (!in_array($filter, $validStatuses)) {
                    $validator->errors()->add('filter', 'Trạng thái xe không hợp lệ');
                }
            }

            if ($type === 'vehicle_loan') {
                $validFilters = ['total_loans', 'not_returned', 'returned_not_ready_count', 'total_fuel_cost', 'total_maintenance_cost'];
                if (!in_array($filter, $validFilters)) {
                    $validator->errors()->add('filter', 'Bộ lọc lượt mượn không hợp lệ');
                }
            }

            if ($type === 'warning') {
                $validWarnings = ['inspection', 'liability_insurance', 'body_insurance'];
                if (!in_array($filter, $validWarnings)) {
                    $validator->errors()->add('filter', 'Loại cảnh báo không hợp lệ');
                }
            }
        });
    }
}
