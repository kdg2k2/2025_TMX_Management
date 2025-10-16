<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BaseRequest extends FormRequest
{
    /**
     * Override nếu muốn custom table name
     */
    protected $tableName = null;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Tự động detect table name từ Model hoặc namespace
     */
    protected function getTableName()
    {
        if ($this->tableName) {
            return $this->tableName;
        }

        // Lấy namespace: App\Http\Requests\ContractAppendix\StoreRequest
        $namespace = get_class($this);

        // Tách lấy phần sau Requests\ và trước \StoreRequest
        if (preg_match('/Requests\\\\(.+?)\\\\[A-Z][a-z]+Request$/', $namespace, $matches)) {
            $modelName = $matches[1];

            // Kiểm tra xem Model có tồn tại không
            $modelClass = 'App\\Models\\' . $modelName;

            if (class_exists($modelClass)) {
                $model = new $modelClass;
                return $model->getTable();
            }

            // Fallback: tự generate table name theo convention
            return Str::snake(Str::plural($modelName));
        }

        return null;
    }

    /**
     * Lấy comment từ database schema
     */
    protected function getColumnComments($tableName = null)
    {
        $table = $tableName ?: $this->getTableName();

        if (!$table) {
            return [];
        }

        return Cache::remember("table_comments_{$table}", 3600, function () use ($table) {
            try {
                if (!Schema::hasTable($table)) {
                    return [];
                }

                $connection = DB::connection();
                $driver = $connection->getDriverName();
                $database = $connection->getDatabaseName();

                if (!in_array($driver, ['mysql', 'mariadb', 'pgsql'])) {
                    return [];
                }

                $query = match($driver) {
                    'mysql', 'mariadb' => "
                        SELECT COLUMN_NAME, COLUMN_COMMENT
                        FROM INFORMATION_SCHEMA.COLUMNS
                        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                    ",
                    'pgsql' => "
                        SELECT
                            a.attname AS column_name,
                            d.description AS column_comment
                        FROM pg_attribute a
                        LEFT JOIN pg_description d ON d.objoid = a.attrelid AND d.objsubid = a.attnum
                        LEFT JOIN pg_class c ON c.oid = a.attrelid
                        WHERE c.relname = ? AND a.attnum > 0 AND NOT a.attisdropped
                    ",
                    default => null
                };

                if (!$query) {
                    return [];
                }

                $params = $driver === 'pgsql' ? [$table] : [$database, $table];
                $columns = DB::select($query, $params);

                $comments = [];
                foreach ($columns as $column) {
                    $columnName = $column->column_name ?? $column->COLUMN_NAME ?? null;
                    $columnComment = $column->column_comment ?? $column->COLUMN_COMMENT ?? null;

                    if ($columnName && $columnComment) {
                        $comment = explode('-', $columnComment)[0];
                        $comments[$columnName] = trim($comment);
                    }
                }

                return $comments;

            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Format tên attribute: Title Case + trong ngoặc kép
     */
    protected function formatAttributeName($name)
    {
        // Xử lý title case cho tiếng Việt
        $name = mb_strtolower($name, 'UTF-8');
        $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');

        // Đặt trong ngoặc kép
        return '"' . $name . '"';
    }

    /**
     * Tự động map attributes với comment từ database
     */
    public function attributes()
    {
        $comments = $this->getColumnComments();

        // Format các comment
        $formattedComments = [];
        foreach ($comments as $field => $comment) {
            $formattedComments[$field] = $this->formatAttributeName($comment);
        }

        // Map thêm các field đặc biệt nếu cần (override ở class con)
        $customMap = $this->customAttributes();

        // Format custom attributes
        $formattedCustom = [];
        foreach ($customMap as $field => $name) {
            // Nếu đã có ngoặc kép thì giữ nguyên
            if (str_starts_with($name, '"') && str_ends_with($name, '"')) {
                $formattedCustom[$field] = $name;
            } else {
                $formattedCustom[$field] = $this->formatAttributeName($name);
            }
        }

        return array_merge($formattedComments, $formattedCustom);
    }

    /**
     * Override ở class con nếu cần custom thêm attributes
     */
    protected function customAttributes()
    {
        return [];
    }
}
