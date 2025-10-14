<?php
namespace App\Services;

use App\Repositories\FileExtensionRepository;

class FileExtensionService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(FileExtensionRepository::class);
    }
}
