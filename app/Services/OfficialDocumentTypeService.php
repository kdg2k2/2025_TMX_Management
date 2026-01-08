<?php

namespace App\Services;

use App\Repositories\OfficialDocumentTypeRepository;

class OfficialDocumentTypeService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(OfficialDocumentTypeRepository::class);
    }

    public function beforeDelete(int $id)
    {
        $data = $this->repository->findById($id, false);
        $data->load([
            'officialDocuments',
            'incomingOfficialDocuments',
        ]);

        if (count($data->officialDocuments) > 0 || count($data->incomingOfficialDocuments) > 0)
            throw new \Exception('Không thể xoá loại văn bản này vì đã có văn bản liên quan.');

        return $data;
    }
}
