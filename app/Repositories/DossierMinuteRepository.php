<?php
namespace App\Repositories;

use App\Models\DossierMinute;

class DossierMinuteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DossierMinute();
        $this->relations = [
            'plan' => function ($q) {
                $q->with(app(DossierPlanRepository::class)->relations);
            },
            'handover' => function ($q) {
                $q->with(app(DossierHandoverRepository::class)->relations);
            },
            'usageRegister' => function ($q) {
                $q->with([
                    'registeredBy',
                    'plan.contract',
                ]);
            },
            'approvedByUser',
        ];
    }

    public function getType($key)
    {
        return $this->model->getType($key);
    }

    public function getStatus($key)
    {
        return $this->model->getStatus($key);
    }

    public function findByContractId(int $contractId)
    {
        return $this->model->whereHas('plan.contract', function ($query) use ($contractId) {
            $query->where('id', $contractId);
        })->where('status', 'approved')->first();
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['except_status']))
            $query->where('status', '!=', $request['except_status']);
    }

    protected function customSort($query, array $request)
    {
        $query->orderByRaw("CASE WHEN status = 'pending_approval' THEN 0 ELSE 1 END");
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'approval_note',
                'rejection_note',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'plan.contract' => ['name'],
                'approvedByUser' => ['name'],
            ]
        ];
    }

    public function deleteDraftByType(int $typeId, string $type)
    {
        return $this
            ->model
            ->where('status', 'draft')
            ->where('type', $type)
            ->when($type === 'handover', function ($q) use ($typeId) {
                $q->where('dossier_handover_id', $typeId);
            })
            ->when($type === 'plan', function ($q) use ($typeId) {
                $q->where('dossier_plan_id', $typeId);
            })
            ->when($type === 'usage_register', function ($q) use ($typeId) {
                $q->where('dossier_usage_register_id', $typeId);
            })
            ->delete();
    }
}
