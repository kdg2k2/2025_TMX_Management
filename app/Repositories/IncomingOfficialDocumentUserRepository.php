<?php

namespace App\Repositories;

use App\Models\IncomingOfficialDocumentUser;

class IncomingOfficialDocumentUserRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new IncomingOfficialDocumentUser();
        $this->relations = [];
    }
}