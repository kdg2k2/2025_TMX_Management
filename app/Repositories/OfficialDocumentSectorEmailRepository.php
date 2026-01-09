<?php

namespace App\Repositories;

use App\Models\OfficialDocumentSectorEmail;

class OfficialDocumentSectorEmailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new OfficialDocumentSectorEmail();
        $this->relations = [];
    }
}