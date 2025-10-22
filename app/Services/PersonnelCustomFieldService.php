<?php

namespace App\Services;

use App\Repositories\PersonnelCustomFieldRepository;

class PersonnelCustomFieldService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(PersonnelCustomFieldRepository::class);
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['types'] = $this->repository->getType();

        return $res;
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        $array['type'] = $this->repository->getType($array['type']);
        return $array;
    }

    public function getFields()
    {
        return $this->tryThrow(function () {
            return $this->repository->getFields();
        });
    }

    public function findByField(string $field)
    {
        return $this->tryThrow(function () use ($field) {
            return $this->findByKey($field, 'field');
        });
    }
}
