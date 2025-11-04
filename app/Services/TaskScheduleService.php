<?php

namespace App\Services;

use App\Models\TaskSchedule;
use App\Repositories\TaskScheduleRepository;
use Carbon\Carbon;
use Cron\CronExpression;
use Exception;

class TaskScheduleService extends BaseService
{
    public function __construct(
        private UserService $userService,
    ) {
        $this->repository = app(TaskScheduleRepository::class);
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request['next_run_at'] = $this->calculateNextRun($request['cron_expression']);
            $userIds = $request['user_ids'];
            unset($request['user_ids']);

            $data = $this->repository->create($request);
            $data->users()->attach($userIds);

            return $data->fresh();
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request['content'] = str_replace(['<br>', '<br/>', '<br />'], "\n", $request['content']);
            $request['next_run_at'] = $this->calculateNextRun($request['cron_expression']);
            $userIds = $request['user_ids'];
            unset($request['user_ids']);

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
            $schedule = $this->findByKey($code, 'code', false, false);

            if (!$schedule->is_active)
                return;

            // Lấy user_ids từ relation
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

    private function calculateNextRun(string $cronExpression): Carbon
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
                app(WorkTimesheetService::class)->emailSchedule($schedule['is_active'], $schedule['code'], $schedule['subject'] ?? $schedule['name'], $flatEmails, $emailData);
                break;
            case 'PAYROLL_REPORT':
                app(WorkTimesheetService::class)->emailSchedule($schedule['is_active'], $schedule['code'], $schedule['subject'] ?? $schedule['name'], $flatEmails, $emailData);
                break;

            default:
                throw new Exception('Không xác dịnh được nội dung task cần chạy');
        }
    }
}
