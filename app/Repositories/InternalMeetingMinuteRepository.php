<?php

namespace App\Repositories;

use App\Models\InternalMeetingMinute;

class InternalMeetingMinuteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new InternalMeetingMinute();
        $this->relations = [
            'createdBy'
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'week',
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
