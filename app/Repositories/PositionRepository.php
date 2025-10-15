<?php
namespace App\Repositories;

use App\Models\Position;
use App\Repositories\BaseRepository;

class PositionRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Position();
    }
}
