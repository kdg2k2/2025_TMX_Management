<?php
namespace App\Repositories;

use App\Models\DossierHandover;

class DossierHandoverRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DossierHandover();
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
            'user',
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
                // Có 'draft': lấy cả handover có minutes với status tương ứng và không có minutes
                $query->where(function ($subQuery) use ($statusArray) {
                    $subQuery->whereHas('minutes', function ($minuteQuery) use ($statusArray) {
                        $minuteQuery->where('type', 'handover')->whereIn('status', $statusArray);
                    })->orWhereDoesntHave('minutes');
                });
            } else {
                // Không có 'draft': chỉ lấy handover có minutes với status tương ứng
                $query->whereHas('minutes', function ($minuteQuery) use ($statusArray) {
                    $minuteQuery->where('type', 'handover')->whereIn('status', $statusArray);
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

        if (isset($request['type']))
            $query->where('type', $request['type']);
    }

    public function findByIdContractAndYear(int $idContract, int $year = null)
    {
        return $this
            ->model
            ->with($this->relations)
            ->whereHas('plan.contract', function ($q) use ($idContract, $year) {
                $q->where('id', $idContract);
                if ($year) {
                    $q->where('year', $year);
                }
            })
            ->first();
    }

    public function deleteByPlanId(int $id)
    {
        return $this->model->where('dossier_plan_id', $id)->delete();
    }

    public function deleteHandoverInByContractIdAndMinuteStatus(int $id, string $status)
    {
        return $this
            ->model
            ->when($status === 'draft', function ($q) use ($status) {
                // Khi status = 'draft': xóa cả handover có minutes draft và không có minutes
                $q->where(function ($subQuery) use ($status) {
                    $subQuery->whereHas('minutes', function ($minuteQuery) use ($status) {
                        $minuteQuery->where('type', 'handover')->where('status', $status);
                    })->orWhereDoesntHave('minutes');
                });
            }, function ($q) use ($status) {
                // Khi status khác 'draft': chỉ xóa handover có minutes với status tương ứng
                $q->whereHas('minutes', function ($minuteQuery) use ($status) {
                    $minuteQuery->where('type', 'handover')->where('status', $status);
                });
            })
            ->whereHas('plan.contract', function ($q) use ($id) {
                $q->where('id', $id);
            })
            ->where('type', 'in')
            ->delete();
    }

    public function getMaxTimeHandoverInByContractId(int $contractId)
    {
        return $this
            ->model
            ->where('type', 'in')
            ->whereHas('plan.contract', function ($q) use ($contractId) {
                $q->where('id', $contractId);
            })
            ->selectRaw('MAX(times) as max_times')
            ->first()
            ->max_times ?? 0;
    }
}
