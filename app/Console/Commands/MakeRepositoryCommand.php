<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    /**
     * Tên lệnh khi gọi artisan.
     */
    protected $signature = 'make:repository
                            {name : Tên class repository, ví dụ: UserRepository}
                            {--model= : Tên model tương ứng, ví dụ: User}';

    /**
     * Mô tả lệnh.
     */
    protected $description = 'Tạo mới một Repository class trong thư mục app/Repositories';

    /**
     * Thực thi lệnh.
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $model = $this->option('model');

        $repositoryPath = app_path('Repositories/' . $name . '.php');

        // Nếu đã tồn tại thì báo lỗi
        if (File::exists($repositoryPath)) {
            $this->error("❌ Repository [$name] đã tồn tại!");
            return;
        }

        // Đảm bảo thư mục tồn tại
        File::ensureDirectoryExists(app_path('Repositories'));

        // Lấy namespace + class name
        $namespace = 'App\\Repositories';
        $className = pathinfo($name, PATHINFO_FILENAME);

        // Nếu model có, thêm import và constructor
        $modelImport = $model ? "use App\\Models\\{$model};" : '';

        // Template file repository
        $stub = <<<PHP
        <?php

        namespace {$namespace};

        {$modelImport}

        class {$className} extends BaseRepository
        {
            public function __construct()
            {
                \$this->model = new {$model}();
                \$this->relations = [];
            }
        }
        PHP;

        File::put($repositoryPath, $stub);

        $this->info("✅ Repository {$className} được tạo thành công tại: app/Repositories/{$className}.php");
    }

    private function camel($string)
    {
        return Str::camel($string);
    }
}
