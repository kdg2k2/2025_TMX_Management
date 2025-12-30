<?php

namespace App\Services;

use App\Repositories\SystemConfigRepository;

class SystemConfigService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(SystemConfigRepository::class);
    }

    public function getDossierPlanHandoverId()
    {
        return $this->repository->getByKey('dossier_plan_handover_id');
    }

    public function getDossierHandoverReceivedById()
    {
        return $this->repository->getByKey('dossier_handover_received_by');
    }

    public function getProfessionalRecordPlanHandoverId()
    {
        return $this->repository->getByKey('professional_record_plan_handover_id');
    }

    public function getProfessionalRecordHandoverReceivedById()
    {
        return $this->repository->getByKey('professional_record_handover_received_by');
    }
}
