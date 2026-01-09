<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\TaskSchedule;
use App\Models\User;
use App\Services\TaskScheduleService;
use Illuminate\Database\Seeder;

class TaskScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseUserIds = User::whereIn('name', ['Lê Sỹ Doanh', 'Phạm Văn Huân', 'Vũ Thị Kim Oanh', 'Kiều Đăng Anh'])->pluck('id')->unique()->filter()->toArray();
        $managerUserIds = collect(array_unique(array_merge(
            $baseUserIds,
            Department::pluck('manager_id')->toArray()
        )))->filter(fn($i) => !empty($i))->toArray();

        $tasks = [
            [
                'code' => 'WORK_TIMESHEET_REPORT',
                'name' => 'Tự động gửi bảng xuất lưới',
                'description' => 'Tự động gửi khi khi upload xuất lưới và chạy lúc 8h sáng ngày 7 hàng tháng',
                'subject' => 'Bảng xuất lưới chấm công máy',
                'content' => 'Kính gửi quý Thầy/Cô bảng tổng hợp chấm công tháng vừa qua. Đề nghị quý Thầy/Cô tổng hợp và đẩy dữ liệu công lên hệ thống để hệ thống tính lương trước mùng 6.',
                'frequency' => 'monthly',
                'cron_expression' => '0 8 7 * *',
                'is_active' => false,
                'manual_run' => true,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'PAYROLL_REPORT',
                'name' => 'Tự động gửi bảng lương',
                'subject' => 'Dữ liệu bảng lương',
                'content' => 'Kính gửi anh/chị dữ liệu tổng hợp bảng lương tháng vừa qua. (Chi tiết trong file đính kèm)',
                'frequency' => 'monthly',
                'cron_expression' => '0 8 9 * *',
                'description' => 'Chạy lúc 8h sáng ngày 9 hàng tháng',
                'is_active' => false,
                'manual_run' => true,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'ALERT_REPORT_WORK_TIMESHEET_AND_OVERTIME',
                'name' => 'Mail thông báo xuất lưới và tổng hợp công làm thêm.',
                'subject' => 'Thông báo xuất lưới công chấm máy và công ngoài giờ',
                'content' => "Dear quý Thầy/Cô, \n Hôm nay là đến thời gian các phòng môn tổng hợp công làm thêm tháng vừa qua của các thành viên nên đề nghị các phòng thực hiện các công việc sau trước ngày 7 tháng này: \n .Phòng tổng hợp xuất lưới chấm công máy và đẩy lên hệ thống \n .Các phòng  tải biểu mẫu chấm công ngoài giờ, nhập đầy đủ thông tin chấm công và đẩy lên hệ thống khi nhận được bảng xuất lưới từ hệ thống.",
                'frequency' => 'monthly',
                'cron_expression' => '0 8 1 * *',
                'description' => 'Chạy lúc 8h sáng ngày 1 hàng tháng',
                'is_active' => false,
                'manual_run' => true,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'REMIND_REPORT_WORK_TIMESHEET_AND_OVERTIME',
                'name' => 'Nhắc nhở xuất lưới và tổng hợp công làm thêm (Nếu chưa có).',
                'subject' => null,
                'content' => null,
                'frequency' => 'monthly',
                'cron_expression' => '0 8 6 * *',
                'description' => 'Chạy lúc 8h sáng ngày 6 hàng tháng',
                'is_active' => false,
                'manual_run' => true,
                'user_ids' => $managerUserIds,
            ],
            [
                'code' => 'COUNCIL_RATING',
                'name' => 'Mail thông báo hội đồng đánh giá.',
                'subject' => 'Thông báo xin ý kiến đánh giá, xếp loại ABC của các thành viên trong công ty',
                'content' => "Dear quý Thầy/Cô; \n Hiện tại hệ thống đã tổng hợp xếp loại, đánh giá của các phòng. Đề nghị quý Thầy/Cô góp ý để hệ thống hoàn thiện bảng chấm công tháng vừa qua.",
                'frequency' => 'monthly',
                'cron_expression' => '0 8 7 * *',
                'description' => 'Chạy lúc 8h sáng ngày 7 hàng tháng',
                'is_active' => false,
                'manual_run' => true,
                'user_ids' => $managerUserIds,
            ],
            [
                'code' => 'TRAIN_AND_BUS_TICKET',
                'name' => 'Vé tàu xe.',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có đăng ký, duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'PLANE_TICKET',
                'name' => 'Vé máy bay.',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có đăng ký, duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'DEVICE_LOAN',
                'name' => 'Mượn trả thiết bị.',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có đăng ký, duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'LEAVE_REQUEST',
                'name' => 'Nghỉ phép',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có đăng ký, duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'WORK_SCHEDULE',
                'name' => 'Công tác',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có đăng ký, duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'SET_COMPLETED_WORK_SCHEDULES',
                'name' => 'Kết thúc công tác',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => '0 17 * * *',
                'description' => 'Tự chạy vào 5h chiều hàng ngày, kiểm tra lịch công tác có hạn trong ngày thì tự kết thúc công tác',
                'is_active' => true,
                'manual_run' => true,
                'user_ids' => [],
            ],
            [
                'code' => 'DOSSIER_MINUTE',
                'name' => 'Biên bản hồ sơ ngoại nghiệp',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có yc duyệt, đăng ký, phê duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'PROFESSIONAL_RECORD_MINUTE',
                'name' => 'Biên bản hồ sơ chuyên môn',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có yc duyệt, đăng ký, phê duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'DEVICE_FIX',
                'name' => 'Sửa chữa thiết bị',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có đăng ký, duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'REMIND_RETURN_DEVICE',
                'name' => 'Nhắc nhở trả thiết bị',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => '0 8 * * *',
                'description' => 'Tự chạy vào 8h sáng hàng ngày thực hiện kiểm tra danh sách mượn thiết bị, bản ghi nào đã duyệt + chưa trả + quá hạn thì nhắc trả',
                'is_active' => true,
                'manual_run' => true,
                'user_ids' => [],
            ],
            [
                'code' => 'REMIND_FIX_DEVICE',
                'name' => 'Nhắc nhở sửa thiết bị',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => '0 8 * * *',
                'description' => 'Tự chạy vào 8h sáng hàng ngày thực hiện kiểm tra danh sách sửa thiết bị kiểm tra, bản ghi nào đã được duyệt + chưa được đánh dấu đã sửa thì nhắc sửa',
                'is_active' => true,
                'manual_run' => true,
                'user_ids' => [],
            ],
            [
                'code' => 'VEHICLE_LOAN',
                'name' => 'Mượn trả phương tiện',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có đăng ký, duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'VEHICLE_MAINTENANCE_WARNING',
                'name' => 'Nhắc nhở bảo dưỡng phương tiện',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => '0 7 * * *',
                'description' => 'Tự chạy vào 7h sáng hàng ngày, kiểm tra danh sách phương tiện sắp đến hạn bảo dưỡng, đăng kiểm, bảo hiểm để gửi mail nhắc nhở',
                'is_active' => true,
                'manual_run' => true,
                'user_ids' => $baseUserIds,
            ],
            [
                'code' => 'INCOMING_OFFICIAL_DOCUMENT',
                'name' => 'Giao nhiệm vụ văn bản đến',
                'subject' => null,
                'content' => null,
                'frequency' => 'daily',
                'cron_expression' => null,
                'description' => 'Tự chạy khi có đăng ký, duyệt...',
                'is_active' => true,
                'manual_run' => false,
                'user_ids' => $baseUserIds,
            ],
        ];

        $taskScheduleService = app(TaskScheduleService::class);
        foreach ($tasks as $task) {
            if($task['cron_expression'])
            $task['next_run_at'] = $taskScheduleService->calculateNextRun($task['cron_expression']);
            $userIds = $task['user_ids'] ?? [];
            unset($task['user_ids']);

            $data = TaskSchedule::updateOrCreate([
                'code' => $task['code'],
            ], $task);

            $data->users()->sync($userIds);
        }
    }
}
