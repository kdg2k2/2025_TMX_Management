<?php

namespace App\Repositories;

use App\Traits\GuardTraits;

abstract class BaseRepository
{
    use GuardTraits;

    public $model;
    public $relations = [];

    public function getColumns()
    {
        return \Schema::getColumnListing($this->model->getTable());
    }

    public function listColumnIgnore($ignores = [])
    {
        $columns = $this->getColumns();
        return array_filter($columns, fn($item) => !in_array($item, $ignores));
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

    public function findByMultipleKey(array $filters)
    {
        $query = $this->model->query();
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $query->where(function ($subQuery) use ($field, $value) {
                    foreach ($value as $val) {
                        $subQuery->orWhereRaw("BINARY $field = ?", [$val]);
                    }
                });
            } else {
                $query->whereRaw("BINARY $field = ?", [$value]);
            }
        }
        return $query->first();
    }

    public function findByKeys(array $keys, $column)
    {
        return $this->model->where(function ($query) use ($column, $keys) {
            foreach ($keys as $key) {
                $query->orWhereRaw("BINARY $column = ?", [$key]);
            }
        })->get();
    }

    public function findByKey(string $key, $column)
    {
        $query = $this->model->whereRaw("BINARY $column = ?", [$key]);
        return $query->first();
    }

    public function list(array $request = [], ?callable $searchFunc = null)
    {
        $query = $this->model->query();

        if (isset($request['order_by']) && isset($request['sort_by']))
            $query->orderBy($request['order_by'], $request['sort_by']);

        if (isset($request['columns']))
            $query->select($request['columns']);

        if (isset($request['ids']))
            $query->whereIn('ids', $request['ids']);

        $this->applyListFilters($query, $request);

        if (isset($request['search']) && $searchFunc)
            $query->where($searchFunc);

        if (!isset($request['load_relations']))
            $request['load_relations'] = true;

        if (isset($this->relations))
            if ($request['load_relations'] == true)
                $query->with($this->relations);

        if (isset($request['paginate']) && $request['paginate'])
            return $query->paginate($request['per_page'] ?? 10);

        return $query->get();
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

    public function delete(int $id)
    {
        return $this->findById($id)->delete();
    }
}
