<?php
namespace App\Repositories;

use App\Models\DossierUsageRegister;

class DossierUsageRegisterRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DossierUsageRegister();
        $this->relations = [
            'minutes',
            'plan' => function ($q) {
                $q->with([
                    'contract',
                    'details' => function ($q) {
                        $q->with([
                            'type',
                            'province',
                            'commune',
                            'unit',
                        ]);
                    },
                ]);
            },
            'registeredBy',
            'details' => function ($q) {
                $q->with([
                    'type',
                    'province',
                    'commune',
                    'unit',
                ]);
            },
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['minute_status'])) {
            $statusArray = is_array($request['minute_status'])
                ? $request['minute_status']
                : [$request['minute_status']];

            // Kiểm tra xem có 'draft' trong status không
            $hasDraft = in_array('draft', $statusArray);

            if ($hasDraft) {
                // Có 'draft': lấy cả usage_register có minutes với status tương ứng và không có minutes
                $query->where(function ($subQuery) use ($statusArray) {
                    $subQuery->whereHas('minutes', function ($minuteQuery) use ($statusArray) {
                        $minuteQuery->where('type', 'usage_register')->whereIn('status', $statusArray);
                    })->orWhereDoesntHave('minutes');
                });
            } else {
                // Không có 'draft': chỉ lấy usage_register có minutes với status tương ứng
                $query->whereHas('minutes', function ($minuteQuery) use ($statusArray) {
                    $minuteQuery->where('type', 'usage_register')->whereIn('status', $statusArray);
                });
            }
        }

        if (isset($request['contract_id']) || isset($request['year']))
            $query->whereHas('plan.contract', function ($q) use ($request) {
                if (isset($request['contract_id']))
                    $q->where('id', $request['contract_id']);
                if (isset($request['year']))
                    $q->where('year', $request['year']);
            });
    }

    public function deleteByContractIdAndMinuteStatus(array $request)
    {
        $query = $this->model->query();

        if (isset($request['minute_status'])) {
            $statusArray = is_array($request['minute_status'])
                ? $request['minute_status']
                : [$request['minute_status']];

            // Kiểm tra xem có 'draft' trong status không
            $hasDraft = in_array('draft', $statusArray);

            if ($hasDraft) {
                // Có 'draft': lấy cả usage_register có minutes với status tương ứng và không có minutes
                $query->where(function ($subQuery) use ($statusArray) {
                    $subQuery->whereHas('minutes', function ($minuteQuery) use ($statusArray) {
                        $minuteQuery->where('type', 'usage_register')->whereIn('status', $statusArray);
                    })->orWhereDoesntHave('minutes');
                });
            } else {
                // Không có 'draft': chỉ lấy usage_register có minutes với status tương ứng
                $query->whereHas('minutes', function ($minuteQuery) use ($statusArray) {
                    $minuteQuery->where('type', 'usage_register')->whereIn('status', $statusArray);
                });
            }
        }

        if (isset($request['contract_id']) || isset($request['year']))
            $query->whereHas('plan.contract', function ($q) use ($request) {
                if (isset($request['contract_id']))
                    $q->where('id', $request['contract_id']);
                if (isset($request['year']))
                    $q->where('year', $request['year']);
            });

        return $query->delete();
    }

    public function findByContractIdAndMinuteStatus(int $contractId, string $minuteStatus)
    {
        return $this
            ->model
            ->whereHas('plan.contract', function ($q) use ($contractId) {
                $q->where('id', $contractId);
            })
            ->whereHas('minutes', function ($q) use ($minuteStatus) {
                $q
                    ->where('type', 'usage_register')
                    ->where('status', $minuteStatus);
            })
            ->first();
    }
}
