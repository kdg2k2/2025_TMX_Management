<?php

namespace App\Repositories;

class ContractProductRepository extends ContractRepository
{
    public function __construct()
    {
        parent::__construct();
        $this->relations = [
            'type',
            'investor',
            'inspectorUser',
            'executorUser',
            'professionals.user',
            'intermediateCollaborators.user',
            'mainProducts:id,contract_id',
            'intermediateProducts:id,contract_id',
            'productMinutes:id,contract_id,status',
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        parent::applyListFilters($query, $request);
        if (isset($request['contract_product_minute_status']))
            $query->whereHas('productMinutes', fn($q) => $q->where('status', $request['contract_product_minute_status']));
    }

    protected function getSearchConfig(): array
    {
        $config = parent::getSearchConfig();
        $config['relations'] = array_map(
            fn($i) => [
                $i => ['name']
            ], [
                'type',
                'investor',
                'inspectorUser',
                'executorUser',
                'professionals.user',
                'intermediateCollaborators.user',
            ]
        );
        return $config;
    }
}
