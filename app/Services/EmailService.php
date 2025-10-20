<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailService extends BaseService
{
    public function sendMail(string $view, string $subject, array $emails, array $data, array $files = [])
    {
        return $this->tryThrow(function () use ($view, $subject, &$emails, $data, $files) {
            // Thiết lập thông tin đăng nhập email một cách tường minh
            $this->configureMailSettings();

            // lọc trùng email và loại bỏ email rỗng
            $emails = array_unique($emails);
            $emails = array_filter($emails, function ($value) {
                return !empty($value);
            });
            $emails = $this->checkLocalMail($emails);

            // gửi mail
            Mail::send(
                $view,
                $data,
                function ($mess) use ($emails, $subject, $files) {
                    $mess->to($emails);
                    $mess->subject($subject);
                    $mess->from(config('mail.from.address'), config('mail.from.name'));
                    if (!empty($files))
                        array_map(function ($item) use ($mess) {
                            if (file_exists($item))
                                $mess->attach($item);
                        }, $files);
                }
            );
        });
    }

    /**
     * Cấu hình thông tin đăng nhập email một cách tường minh
     */
    private function configureMailSettings()
    {
        $mailConfig = [
            'transport' => 'smtp',
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'timeout' => config('mail.mailers.smtp.timeout'),
            'local_domain' => config('mail.mailers.smtp.local_domain'),
        ];

        // Cập nhật cấu hình mail runtime
        Config::set('mail.mailers.smtp', $mailConfig);
        Config::set('mail.default', 'smtp');

        // Cấu hình thông tin người gửi
        Config::set('mail.from.address', config('mail.from.address'));
        Config::set('mail.from.name', config('mail.from.name'));
    }

    public function checkLocalMail(array $emails)
    {
        if ($this->isLocal() == true)
            $emails = ['dangnguyen.xmg@xuanmaijsc.vn'];
        return $emails;
    }
}
