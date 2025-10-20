<?php

namespace App\Jobs;

use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 0;        // Thử vô tận (0 = unlimited)
    public $retryAfter = 5;   // Sau 5 giây mới thử lại (sẽ tăng dần)
    public $maxExceptions = 0; // Không giới hạn số exception
    protected $view;
    protected $subject;
    protected $emails;
    protected $data;
    protected $files;

    public function __construct($view, $subject, $emails, $data, $files = [])
    {
        $this->view = $view;
        $this->subject = $subject;
        $this->emails = $emails;
        $this->data = $data;
        $this->files = $files;

        // Đặt queue trong constructor để tránh conflict với trait Queueable
        $this->onQueue('emails');
    }

    public function handle()
    {
        app(EmailService::class)->sendMail(
            $this->view,
            $this->subject,
            $this->emails,
            $this->data,
            $this->files
        );
    }

    /**
     * Determine if the job should be retried based on the exception.
     */
    public function retryUntil()
    {
        // Thử trong 1 tuần (7 ngày)
        // Job sẽ được retry liên tục trong 1 tuần kể từ lần đầu tiên được dispatch
        return now()->addWeek();
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff()
    {
        // Exponential backoff: tăng dần thời gian delay
        return [
            10,     // 10 giây
            20,     // 20 giây
            40,     // 40 giây
            60,     // 1 phút
            300,    // 5 phút
            600,    // 10 phút
            1800,   // 30 phút (và tiếp tục 30 phút cho các lần sau)
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        // Log lỗi khi job thực sự fail
        Log::error('SendMailJob failed permanently', [
            'view' => $this->view,
            'subject' => $this->subject,
            'emails' => $this->emails,
            'exception' => $exception->getMessage()
        ]);
    }
}
