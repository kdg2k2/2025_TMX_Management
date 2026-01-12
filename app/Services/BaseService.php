<?php

namespace App\Services;

use App\Traits\AssetPathTraits;
use App\Traits\CheckLocalTraits;
use App\Traits\FormatDateTraits;
use App\Traits\GuardTraits;
use App\Traits\TryCatchTraits;

abstract class BaseService
{
    use TryCatchTraits,
        CheckLocalTraits,
        AssetPathTraits,
        FormatDateTraits,
        GuardTraits;

    public $repository;

    public function getColumns()
    {
        return $this->repository->getColumns();
    }

    public function count()
    {
        return $this->tryThrow(function () {
            return $this->repository->count();
        });
    }

    public function getIds()
    {
        return $this->tryThrow(function () {
            return $this->repository->getIds();
        });
    }

    /**
     * Find entity by ID
     */
    public function findById(int $id, bool $loadRelation = true, bool $returnFormatRecord = false)
    {
        return $this->tryThrow(function () use ($id, $loadRelation, $returnFormatRecord) {
            $data = $this->repository->findById($id, $loadRelation);
            if ($data && $returnFormatRecord)
                return $this->formatRecord($data->toArray());
            return $data;
        });
    }

    public function findByMultipleKey(array $filters)
    {
        return $this->tryThrow(function () use ($filters) {
            return $this->repository->findByMultipleKey($filters);
        });
    }

    public function findByKey(string $key, string $column, bool $useBinarySearch = true, bool $loadRelation = true, array $customRelations = [])
    {
        return $this->tryThrow(function () use ($key, $column, $useBinarySearch, $loadRelation, $customRelations) {
            return $this->repository->findByKey($key, $column, $useBinarySearch, $loadRelation, $customRelations);
        });
    }

    public function findByKeys(array $keys, $column)
    {
        return $this->tryThrow(function () use ($keys, $column) {
            return $this->repository->findByKeys($keys, $column);
        });
    }

    /**
     * Find entity by name
     */
    public function findByName(string $name)
    {
        return $this->tryThrow(function () use ($name) {
            return $this->repository->findByKey($name, 'name');
        });
    }

    /**
     * Find entity by name
     */
    public function findByNames(array $names)
    {
        return $this->tryThrow(function () use ($names) {
            return $this->repository->findByKeys($names, 'name');
        });
    }

    protected function beforeListQuery(array $request)
    {
        return $request;
    }

    /**
     * Get list of entities
     */
    public function list(array $request = [])
    {
        return $this->tryThrow(function () use ($request) {
            $request = $this->beforeListQuery($request);

            $data = $this->repository->list($request);

            if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $data->getCollection()->transform(function ($item) {
                    return $this->formatRecord($item->toArray());
                });
                return $data;
            }

            if (gettype($data) === 'object')
                $data = $data->toArray();
            $data = $this->formatRecords($data);
            return $data;
        });
    }

    /**
     * Import array entity
     */
    public function insert(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request = $this->beforeImport($request);
            $data = $this->repository->insert($request);
            $this->afterImport($data, $request);
        }, true);
    }

    /**
     * Store new entity
     */
    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // Pre-processing hook
            $request = $this->beforeStore(request: $request);
            $extract = $this->extractBefore($request);

            // Store entity
            $data = $this->repository->store($extract['request']);

            // Post-processing hook
            $this->afterStore($data, $extract['request']);
            $this->handleExtractAfter($extract, $data);

            return $data;
        }, true);
    }

    /**
     * Update entity
     */
    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // Pre-processing hook
            $request = $this->beforeUpdate($request);
            $extract = $this->extractBefore($request);

            // Update entity
            $data = $this->repository->update($extract['request']);

            // Post-processing hook
            $this->afterUpdate($data, $extract['request']);
            $this->handleExtractAfter($extract, $data);

            return $data;
        }, true);
    }

    /**
     * Delete entity
     */
    public function delete(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            // Pre-processing hook
            $entity = $this->beforeDelete($id);

            // Delete entity
            $result = $this->repository->delete($id);

            // Post-processing hook
            $this->afterDelete($entity);

            return $result;
        }, true);
    }

    /**
     * Format single record - override in child classes
     */
    public function formatRecord(array $array)
    {
        if (isset($array['created_at']))
            $array['created_at'] = $this->formatDateTimeForPreview($array['created_at']);
        if (isset($array['updated_at']))
            $array['updated_at'] = $this->formatDateTimeForPreview($array['updated_at']);
        return $array;
    }

    /**
     * Format multiple records
     */
    public function formatRecords(array $array)
    {
        return array_map([$this, 'formatRecord'], $array);
    }

    // Hook methods - override in child classes as needed

    /**
     * Pre-processing before insert
     */
    protected function beforeImport(array $request)
    {
        return $request;
    }

    /**
     * Post-processing after insert
     */
    protected function afterImport($data, array $request)
    {
        // Override in child classes
    }

    /**
     * Pre-processing before store
     */
    protected function beforeStore(array $request)
    {
        return $request;
    }

    /**
     * Post-processing after store
     */
    protected function afterStore($data, array $request)
    {
        // Override in child classes
    }

    /**
     * Pre-processing before update
     */
    protected function beforeUpdate(array $request)
    {
        return $request;
    }

    /**
     * Post-processing after update
     */
    protected function afterUpdate($data, array $request)
    {
        // Override in child classes
    }

    /**
     * Pre-processing before delete
     */
    protected function beforeDelete(int $id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Post-processing after delete
     */
    protected function afterDelete($entity)
    {
        // Override in child classes
    }

    protected function syncRelationship($model, $foreignKey, string $relation, array $items)
    {
        $model->$relation()->delete();

        if (count($items) == 0)
            return;

        $data = collect($items)->map(function ($item) use ($model, $foreignKey) {
            $item[$foreignKey] = $model['id'];
            $item['created_at'] = now();
            $item['updated_at'] = now();
            return $item;
        })->toArray();

        if (!empty($data))
            $model->$relation()->insert($data);
    }

    protected function extractBefore(array $request)
    {
        return [
            'request' => $request,
        ];
    }

    protected function handleExtractAfter(array $extract, $data) {}
}
