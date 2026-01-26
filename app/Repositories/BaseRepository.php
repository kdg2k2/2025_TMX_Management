<?php

namespace App\Repositories;

use App\Traits\GuardTraits;

abstract class BaseRepository
{
    use GuardTraits;

    public $model;
    public $relations = [];

    public function getUniqueColumn(string $column, string $sortBy = 'id', string $orderBy = 'desc')
    {
        return $this->model->orderBy($sortBy, $orderBy)->pluck($column)->unique()->toArray();
    }

    public function getColumns()
    {
        return \Schema::getColumnListing($this->model->getTable());
    }

    public function listColumnIgnore($ignores = [])
    {
        $columns = $this->getColumns();
        return array_filter($columns, fn($item) => !in_array($item, $ignores));
    }

    public function count(string $tableName = null)
    {
        if ($tableName) {
            return \DB::table($tableName)->count();
        }
        return $this->model->count();
    }

    public function getTable()
    {
        return $this->model->getTable();
    }

    public function getIds()
    {
        return $this->model->pluck('id')->toArray();
    }

    public function findById(int $id, bool $loadRelation = true)
    {
        $data = $this->model->find($id);
        if (!empty($this->relations) && $loadRelation == true)
            $data = $data->load($this->relations);

        return $data;
    }

    public function findByMultipleKeys(array $filters, bool $useOrwhere = false)
    {
        $query = $this->model->query();
        $method = $useOrwhere ? 'orWhere' : 'where';

        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $query->$method(fn($q) => $q->whereIn($field, $value));
            } else {
                $query->$method($field, $value);
            }
        }

        return $query->first();
    }

    public function findByKeys(array $keys, string $column, bool $loadRelation = false)
    {
        $query = $this->model->query();
        if ($loadRelation)
            $query->with($this->relations);

        return $query->where(function ($query) use ($column, $keys) {
            foreach ($keys as $key) {
                $query->orWhere($column, $key);
            }
        })->get();
    }

    public function findByKey(string $key, string $column, bool $useBinarySearch = true, bool $loadRelation = true, array $customRelations = [])
    {
        if ($useBinarySearch)
            $query = $this->model->whereRaw("BINARY $column = ?", [$key]);
        else
            $query = $this->model->where($column, $key);

        $mergeRelations = array_merge($loadRelation ? $this->relations : [], $customRelations);
        if (!empty($mergeRelations))
            $query->with($mergeRelations);

        return $query->first();
    }

    protected function customSort($query, array $request) {}

    protected function applySort($query, array $request)
    {
        if (isset($request['custom_sort']) && ($request['custom_sort'] ?? false))
            $this->customSort($query, $request);

        // Sort mặc định
        $orderBy = $request['order_by'] ?? 'id';
        $sortBy = $request['sort_by'] ?? 'desc';
        $query->orderBy($orderBy, $sortBy);
    }

    public function list(array $request = [])
    {
        $query = $this->model->query();

        if (isset($request['ids']))
            $query->whereIn('id', $request['ids']);

        $this->applyListFilters($query, $request);

        if (isset($request['columns']))
            $query->select($request['columns']);

        if (isset($request['search']))
            $this->applySearch($query, $request['search']);

        $relationsToLoad = [];
        if (isset($this->relations) && ($request['load_relations'] ?? true))
            $relationsToLoad = array_merge($relationsToLoad, $this->relations);
        if (isset($request['custom_relations']))
            $relationsToLoad = array_merge($relationsToLoad, $request['custom_relations']);
        if (!empty($relationsToLoad))
            $query->with($relationsToLoad);

        $this->applySort($query, $request);

        if (isset($request['paginate']) && $request['paginate'])
            return $query->paginate($request['per_page'] ?? 10);

        return $query->get();
    }

    /**
     * Apply search với cấu hình mặc địnhapplySearch
     */
    protected function applySearch($query, string $search)
    {
        $config = $this->getSearchConfig();
        $config['datetime'] = array_merge($config['datetime'], ['created_at', 'updated_at']);
        app(\App\Services\SearchService::class)->applySearch($query, $search, $config);
    }

    /**
     * Định nghĩa cấu hình search - các class con override
     */
    protected function getSearchConfig(): array
    {
        return [
            'text' => [],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }

    protected function applyListFilters($query, array $request) {}

    public function maxId()
    {
        return $this->model->max('id');
    }

    public function insert(array $request)
    {
        $maxId = $this->maxId();
        $request = array_map(function ($item) use (&$maxId) {
            $maxId++;
            $item['id'] = $maxId;
            return $item;
        }, $request);
        return $this->model->insert($request);
    }

    public function store(array $request)
    {
        $request['id'] = $this->maxId() + 1;  // Tự động tăng ID
        return $this->model->create($request);
    }

    public function update(array $request)
    {
        $model = $this->findById($request['id']);
        $model->update($request);
        return $model;
    }

    public function updateOrCreate(array $uniqueBy, array $values)
    {
        return $this->model->updateOrCreate($uniqueBy, $values);
    }

    public function upsert(
        array $values,
        array $uniqueBy,
        array $update = null
    ) {
        return $this->model->upsert(
            $values,
            $uniqueBy,
            $update
        );
    }

    public function delete(int $id)
    {
        return $this->findById($id)->delete();
    }
}
