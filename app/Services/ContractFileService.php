<?php
namespace App\Services;

use App\Repositories\ContractFileRepository;

class ContractFileService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractFileRepository::class);
    }

    public function viewFile(int $id)
    {
        $data = $this->repository->findById($id, false);
        return $this->getAssetUrl($data['path']);
    }
}
