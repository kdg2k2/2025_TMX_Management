<?php

namespace App\Services;

use App\Traits\GetValueFromArrayByKeyTraits;

class UserTimeTableService extends BaseService
{
    use GetValueFromArrayByKeyTraits;

    public function __construct(
        private DateService $dateService,
        private UserService $userService,
        private WorkScheduleService $workScheduleService,
        private LeaveRequestService $leaveRequestService,
        private DepartmentService $departmentService
    ) {}

    public function getBaseListData()
    {
        return $this->tryThrow(function () {
            $curentYear = (int) date('Y');
            $years = range($curentYear, 2024);
            $weeks = $this->getWeeks((int) date('Y'));
            $currentWeekNumber = collect($weeks)->firstWhere('is_current', true)['week_number'] ?? null;

            return [
                'currentYear' => $curentYear,
                'currentWeekNumber' => $currentWeekNumber,
                'years' => $years,
                'weeks' => $weeks,
                'departments' => $this->departmentService->list([
                    'load_relations' => false,
                ]),
            ];
        });
    }

    public function getWeeks(int $year)
    {
        return $this->tryThrow(function () use ($year) {
            return $this->dateService->getWeeksOfYear($year);
        });
    }

    public function list(array $request = [])
    {
        return $this->tryThrow(function () use ($request) {
            if (!isset($request['week']))
                $request['week'] = $this->dateService->getWeekFromDate(date('Y-m-d'));
            if (!isset($request['year']))
                $request['year'] = (int) date('Y');

            $daysInWeek = $this->dateService->getDaysInWeek($request['week'], $request['year']);
            $firstDay = $daysInWeek[0]['date'];
            $lastDay = $daysInWeek[6]['date'];

            // Lấy danh sách users với custom sort
            $users = $this->userService->list([
                'paginate' => false,
                'custom_sort' => true,
                'is_banned' => false,
                'retired' => false,
                'department_id' => $request['department_id'] ?? null,
                'custom_relations' => [
                    'warning',
                ],
            ]);

            $userIds = array_column($users, 'id');

            // Lấy tất cả work schedules và leave requests trong tuần
            $workSchedules = $this
                ->workScheduleService
                ->getBaseQueryForDateRange($firstDay, $lastDay)
                ->whereIn('created_by', $userIds)
                ->with($this
                    ->workScheduleService
                    ->repository
                    ->relations)
                ->get()
                ->groupBy('created_by');

            $leaveRequests = $this
                ->leaveRequestService
                ->getBaseQueryForDateRange($firstDay, $lastDay)
                ->whereIn('created_by', $userIds)
                ->with($this
                    ->leaveRequestService
                    ->repository
                    ->relations)
                ->get()
                ->groupBy('created_by');

            // Chuẩn bị cấu trúc dữ liệu theo ngày
            $result = [];
            foreach ($daysInWeek as $day) {
                $dayDate = $day['date'];
                $dayData = [
                    'date' => $dayDate,
                    'day_of_week' => $day['day_of_week'],
                    'day_name' => $day['day_name'],
                    'day_name_short' => $day['day_name_short'],
                    'users' => []
                ];

                // Kiểm tra nếu là Chủ nhật (day_of_week = 0)
                $isDayOff = $day['day_of_week'] === 0;

                // Duyệt qua từng user để xác định trạng thái trong ngày này
                foreach ($users as $user) {
                    $userId = $user['id'];
                    $userWorkSchedules = $workSchedules->get($userId, collect());
                    $userLeaveRequests = $leaveRequests->get($userId, collect());

                    // Mặc định là ngày nghỉ nếu là Chủ nhật
                    $status = $isDayOff ? 'day_off' : 'working';
                    $details = null;

                    // Kiểm tra công tác
                    foreach ($userWorkSchedules as $workSchedule) {
                        if ($dayDate >= $workSchedule['from_date'] && $dayDate <= $workSchedule['to_date']) {
                            $status = 'business_trip';
                            $details = [
                                'type' => 'work_schedule',
                                'detail' => $this
                                    ->workScheduleService
                                    ->formatRecord($workSchedule->toArray()),
                            ];
                            break;
                        }
                    }

                    // Nếu không công tác, kiểm tra nghỉ phép
                    if ($status !== 'business_trip' && !$isDayOff) {
                        foreach ($userLeaveRequests as $leaveRequest) {
                            if ($dayDate >= $leaveRequest['from_date'] && $dayDate <= $leaveRequest['to_date']) {
                                $status = 'on_leave';
                                $details = [
                                    'type' => 'leave_request',
                                    'detail' => $this
                                        ->leaveRequestService
                                        ->formatRecord($leaveRequest->toArray()),
                                ];
                                break;
                            }
                        }
                    }

                    // Thêm thông tin user vào ngày
                    $info = [
                        'status' => $this->getStatus($status),
                        'warning' => collect($user['warning'] ?? [])->filter(fn($i) => $i['warning_date'] == $dayDate)->first(),
                        'details' => $details,
                        'user_id' => $userId,
                        'user_name' => $user['name'],
                        'path' => $user['path'],
                        'email' => $user['email'],
                        'department' => $user['department']['name'] ?? null,
                        'position' => $user['position']['name'] ?? null,
                        'job_title' => $user['job_title']['name'] ?? null,
                    ];
                    $dayData['users'][] = $info;
                }

                usort($dayData['users'], function ($a, $b) {
                    $priorityA = $a['status']['priority'] ?? 999;
                    $priorityB = $b['status']['priority'] ?? 999;
                    return $priorityA - $priorityB;
                });

                $result[] = $dayData;
            }

            return $result;
        });
    }

    private function getStatus(string $key)
    {
        $statuses = [
            'working' => [
                'original' => 'working',
                'converted' => 'Làm việc tại cơ quan',
                'color' => 'success',
                'priority' => 3,
            ],
            'business_trip' => [
                'original' => 'business_trip',
                'converted' => 'Đi công tác',
                'color' => 'warning',
                'priority' => 1,
            ],
            'on_leave' => [
                'original' => 'on_leave',
                'converted' => 'Nghỉ phép',
                'color' => 'danger',
                'priority' => 2,
            ],
            'day_off' => [
                'original' => 'day_off',
                'converted' => 'Ngày nghỉ',
                'color' => 'outline-light border',
                'priority' => 4,
            ],
        ];

        return $this->getValueFromArrayByKey($statuses, $key);
    }
}
