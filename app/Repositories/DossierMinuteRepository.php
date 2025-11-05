<?php
namespace App\Repositories;

use App\Models\DossierMinute;

class DossierMinuteRepository extends BaseRepository
{
    public $baseRelation = [];

    public function __construct()
    {
        $this->model = new DossierMinute();
        $this->baseRelation = [
            'user',
            'handoverBy',
            'receivedBy',
        ];
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

    public function listExceptDraftSortByStatus(array $request)
    {
        $query = $this
            ->model
            ->where('status', '!=', 'draft')
            ->orderByRaw("CASE WHEN status = 'pending_approval' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->with($this->relations);

        if (!empty($request['search'])) {
            $search = "%{$request['search']}%";
            $query->where(function ($q) use ($search) {
                $q
                    ->whereHas('plan.contract', function ($q) use ($search) {
                        $q->where('tenhd', 'like', $search);
                    })
                    ->orWhereHas('approvedByUser', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    })
                    ->orWhere('approval_note', 'like', $search)
                    ->orWhere('rejection_note', 'like', $search);
            });
        }

        return $query->get()->toArray();
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
