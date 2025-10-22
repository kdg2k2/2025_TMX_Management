<?php

namespace App\Services;

use App\Models\Personnel;
use App\Repositories\PersonnelRepository;
use Exception;

class PersonnelService extends BaseService
{
    public function __construct(
        private PersonnelUnitService $personnelUnitService,
        private PersonnelCustomFieldService $personnelCustomFieldService
    ) {
        $this->repository = app(PersonnelRepository::class);
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['personnelUnits'] = $this->personnelUnitService->list();
        $res['fields'] = $this->personnelCustomFieldService->list();

        return $res;
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->store($request);
            $this->handleExtracted($data, $extracted);
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->update($request);
            $this->handleExtracted($data, $extracted, true);
        }, true);
    }

    private function extractFields(array &$request): array
    {
        $fields = $this->personnelCustomFieldService->getFields();

        $extracted = [];
        foreach ($fields as $field) {
            $extracted[$field] = $request[$field] ?? null;
            unset($request[$field]);
        }

        return $extracted;
    }

    private function handleExtracted(Personnel $data, array $extracted, bool $isUpdate = false)
    {
        $extracted = $this->formatFields($data, $extracted);
        $this->personnelPivotPersonnelCustomField($data, $extracted);
    }

    private function formatFields(Personnel $data, array $fields)
    {
        $res = [];
        foreach ($fields as $key => $value) {
            $personnelCustomFieldId = optional($this->personnelCustomFieldService->findByField($key))->id ?? null;
            if (!$personnelCustomFieldId)
                throw new Exception('Không tìm thấy field');

            $res[] = [
                'personnel_id' => $data['id'],
                'personnel_custom_field_id' => $personnelCustomFieldId,
                'value' => $value,
            ];
        }
        return $res;
    }

    private function personnelPivotPersonnelCustomField(Personnel $data, array $pivot)
    {
        $data->personnelPivotPersonnelCustomField()->delete();

        if (count($pivot) == 0)
            return;

        $data->personnelPivotPersonnelCustomField()->insert($pivot);
    }
}
