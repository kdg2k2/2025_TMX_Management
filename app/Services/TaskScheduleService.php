<?php

namespace App\Services;

use App\Models\TaskSchedule;
use App\Repositories\TaskScheduleRepository;
use Carbon\Carbon;
use Cron\CronExpression;

class TaskScheduleService extends BaseService
{
    public function __construct(
        private UserService $userService,
    ) {
        $this->repository = app(TaskScheduleRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        $array['frequency'] = $this->getFrequency($array['frequency']);
        if (isset($array['last_run_at']))
            $array['last_run_at'] = $this->formatDateTimeForPreview($array['last_run_at']);
        if (isset($array['next_run_at']))
            $array['next_run_at'] = $this->formatDateTimeForPreview($array['next_run_at']);
        return $array;
    }

    public function getBaseUpdateView(int $id)
    {
        return [
            'data' => $this->repository->findById($id),
            'frequency' => $this->getFrequency(),
            'users' => $this->userService->list([
                'load_relations' => false,
                'columns' => ['id', 'name'],
            ]),
        ];
    }

    public function getFrequency($key = null)
    {
        return $this->tryThrow(fn() => $this->repository->getFrequency($key));
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request['content'] = str_replace(['<br>', '<br/>', '<br />'], "\n", $request['content']);
            if (isset($request['next_run_at']) && $request['next_run_at']) {
                $request['next_run_at'] = Carbon::parse($request['next_run_at']);
            } else {
                $request['next_run_at'] = $this->calculateNextRun($request['cron_expression']);
            }
            $userIds = $request['users'];
            unset($request['users']);

            $data = $this->repository->update($request);
            $data->users()->sync($userIds);

            return $data->fresh();
        }, true);
    }

    public function toggleActive(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            $data = $this->repository->findById($id);
            $data->update([
                'is_active' => !$data->is_active,
            ]);

            return $data->fresh();
        }, true);
    }

    public function run(string $code)
    {
        return $this->tryThrow(function () use ($code) {
            $schedule = $this->findByKey($code, 'code', true, false);

            if (!$schedule || !isset($schedule->is_active))
                return;

            // Lấy users từ relation
            $userIds = $schedule->users->pluck('id')->toArray();

            // Lấy tất cả emails (bao gồm cả sub_emails) qua UserService
            $emails = $this->userService->getEmails($userIds);

            // Chuẩn bị data cho email
            $emailData = [
                'content' => str_replace('\n', "\n", $schedule->content),
                'scheduleName' => $schedule->name,
                'runAt' => now()->format('d/m/Y H:i:s'),
            ];

            $this->dispatchTask($schedule, $emails, $emailData);

            // Cập nhật lần chạy tiếp theo
            $schedule->update([
                'last_run_at' => now(),
                'next_run_at' => $this->calculateNextRun($schedule->cron_expression),
            ]);
        });
    }

    public function calculateNextRun(string $cronExpression): Carbon
    {
        $cron = new CronExpression($cronExpression);
        return Carbon::instance($cron->getNextRunDate());
    }

    public function sendMail(string $subject = null, array $emails = [], array $data = [], array $files = [])
    {
        dispatch(new \App\Jobs\SendMailJob('emails.task-schedule', $subject, $emails, $data, $files));
    }

    private function dispatchTask(TaskSchedule $schedule, array $flatEmails, array $emailData)
    {
        switch ($schedule['code']) {
            case 'WORK_TIMESHEET_REPORT':
            case 'PAYROLL_REPORT':
                app(WorkTimesheetService::class)->emailSchedule($schedule['is_active'], $schedule['code'], $schedule['subject'] ?? $schedule['name'], $flatEmails, $emailData);
                break;
            case 'SET_COMPLETED_WORK_SCHEDULES':
                app(WorkScheduleService::class)->setCompletedWorkSchedules();
                break;
            default:
                $this->sendMail($schedule['subject'] ?? $schedule['name'], $flatEmails = [], $emailData);
                break;
        }
    }

    public function getUserIdByScheduleKey(string $key)
    {
        $schedule = $this->findByKey($key, 'code', false, false, ['emails']);
        if ($schedule['is_active'] == 0)
            return [];
        return $schedule['emails']->pluck('user_id')->unique()->filter()->toArray();
    }
}
