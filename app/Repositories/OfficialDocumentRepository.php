<?php

namespace App\Repositories;

use App\Models\OfficialDocument;

class OfficialDocumentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new OfficialDocument();
        $this->relations = [];
    }
}