<?php
namespace App\Services;

class DossierPlanDetailService extends BaseService
{
    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['estimated_time']))
            $array['estimated_time'] = $this->formatDateForPreview($array['estimated_time']);

        return $array;
    }
}
