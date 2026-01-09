<?php

namespace App\Repositories;

use App\Models\OfficialDocumentEmail;

class OfficialDocumentEmailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new OfficialDocumentEmail();
        $this->relations = [];
    }
}