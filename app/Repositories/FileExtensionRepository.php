<?php
namespace App\Repositories;

use App\Models\FileExtension;

class FileExtensionRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new FileExtension();
    }
}
