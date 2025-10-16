<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    /**
     * Tên lệnh (khi gọi artisan)
     */
    protected $signature = 'make:service {name : Tên class service, ví dụ: UserService}';

    /**
     * Mô tả lệnh (hiện khi chạy php artisan list)
     */
    protected $description = 'Tạo mới một Service class trong thư mục app/Services';

    /**
     * Thực thi lệnh
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $servicePath = app_path('Services/' . $name . '.php');

        // Kiểm tra trùng tên
        if (File::exists($servicePath)) {
            $this->error("❌ Service [$name] đã tồn tại!");
            return;
        }

        // Tạo folder nếu chưa có
        File::ensureDirectoryExists(app_path('Services'));

        // Lấy namespace và class name
        $namespace = 'App\\Services';
        $className = pathinfo($name, PATHINFO_FILENAME);

        // Template nội dung file
        $stub = <<<PHP
        <?php

        namespace {$namespace};

        class {$className} extends BaseService
        {
            public function __construct()
            {
                // \$this->repository = app();
            }
        }
        PHP;

        // Ghi file
        File::put($servicePath, $stub);

        $this->info("✅ Service {$className} được tạo thành công tại: app/Services/{$className}.php");
    }
}
