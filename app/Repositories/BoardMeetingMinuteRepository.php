<?php

namespace App\Repositories;

use App\Models\BoardMeetingMinute;

class BoardMeetingMinuteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BoardMeetingMinute();
        $this->relations = [
            'createdBy'
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'number',
                'main_content',
            ],
            'date' => [
                'meeting_day',
            ],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name']
            ]
        ];
    }
}
