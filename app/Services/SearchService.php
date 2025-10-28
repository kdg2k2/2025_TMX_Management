<?php
namespace App\Services;

use App\Services\StringHandlerService;
use Carbon\Carbon;

class SearchService
{
    public function __construct(
        private StringHandlerService $stringHandlerService,
        private DateService $dateService
    ) {}

    public function applySearch($query, string $searchTerm, array $config = [])
    {
        $searchTerm = trim($searchTerm);

        if (empty($searchTerm)) {
            return $query;
        }

        // Prepare các biến cần thiết
        $searchTermNoAccent = $this->stringHandlerService->removeAccents($searchTerm);
        $parsedDate = $this->dateService->parseDate($searchTerm);
        $parsedDateTime = $this->dateService->parseDateTime($searchTerm);

        $query->where(function ($q) use ($config, $searchTerm, $searchTermNoAccent, $parsedDate, $parsedDateTime) {
            // Search trong các columns của model chính
            $this->applyMainModelSearch($q, $config, $searchTerm, $searchTermNoAccent, $parsedDate, $parsedDateTime);

            // Search trong relations
            if (!empty($config['relations'])) {
                $this->applyRelationSearch($q, $config['relations'], $searchTerm, $searchTermNoAccent, $parsedDate, $parsedDateTime);
            }
        });

        return $query;
    }

    /**
     * Apply search cho model chính
     */
    protected function applyMainModelSearch($query, $config, $searchTerm, $searchTermNoAccent, $parsedDate, $parsedDateTime)
    {
        // Text columns
        if (!empty($config['text'])) {
            $this->applyTextSearch($query, $config['text'], $searchTerm, $searchTermNoAccent);
        }

        // Date columns
        if (!empty($config['date'])) {
            // Nếu parse được full date (Y-m-d)
            if ($parsedDate) {
                $this->applyDateSearch($query, $config['date'], $parsedDate);
            }
            // Nếu user nhập day/month (vd "18/10") -> áp dụng cho DATE columns
            elseif ($parsedDateTime && isset($parsedDateTime['type']) && $parsedDateTime['type'] === 'day_month') {
                $this->applyDateDayMonthSearch($query, $config['date'], $parsedDateTime['day'], $parsedDateTime['month']);
            }
            // Nếu user nhập tháng/năm (vd "10/2025")
            elseif ($parsedDateTime && isset($parsedDateTime['type']) && $parsedDateTime['type'] === 'month') {
                $this->applyDateMonthYearSearch($query, $config['date'], $parsedDateTime['month'], $parsedDateTime['year']);
            }
            // Nếu user nhập chỉ năm (vd "2025")
            elseif ($parsedDateTime && isset($parsedDateTime['type']) && $parsedDateTime['type'] === 'year') {
                $this->applyDateYearSearch($query, $config['date'], $parsedDateTime['year']);
            }
        }

        // Datetime columns
        if (!empty($config['datetime'])) {
            $this->applyDateTimeSearch($query, $config['datetime'], $searchTerm, $parsedDateTime);
        }
    }

    /**
     * Apply search trong relations với support date/datetime
     */
    protected function applyRelationSearch($query, array $relations, string $searchTerm, string $searchTermNoAccent, $parsedDate, $parsedDateTime)
    {
        foreach ($relations as $relation => $columns) {
            $query->orWhereHas($relation, function ($q) use ($columns, $searchTerm, $searchTermNoAccent, $parsedDate, $parsedDateTime) {
                $q->where(function ($subQ) use ($columns, $searchTerm, $searchTermNoAccent, $parsedDate, $parsedDateTime) {
                    // Nếu columns là array đơn giản (backward compatibility)
                    if (isset($columns[0]) && is_string($columns[0])) {
                        $this->applyTextSearch($subQ, $columns, $searchTerm, $searchTermNoAccent);
                    }
                    // Nếu columns có structure với text/date/datetime
                    else {
                        // Text columns trong relation
                        if (!empty($columns['text'])) {
                            $this->applyTextSearch($subQ, $columns['text'], $searchTerm, $searchTermNoAccent);
                        }

                        // Date columns trong relation
                        if (!empty($columns['date'])) {
                            if ($parsedDate) {
                                $this->applyDateSearch($subQ, $columns['date'], $parsedDate);
                            } elseif ($parsedDateTime && isset($parsedDateTime['type'])) {
                                if ($parsedDateTime['type'] === 'day_month') {
                                    $this->applyDateDayMonthSearch($subQ, $columns['date'], $parsedDateTime['day'], $parsedDateTime['month']);
                                } elseif ($parsedDateTime['type'] === 'month') {
                                    $this->applyDateMonthYearSearch($subQ, $columns['date'], $parsedDateTime['month'], $parsedDateTime['year']);
                                } elseif ($parsedDateTime['type'] === 'year') {
                                    $this->applyDateYearSearch($subQ, $columns['date'], $parsedDateTime['year']);
                                }
                            }
                        }

                        // Datetime columns trong relation
                        if (!empty($columns['datetime'])) {
                            $this->applyDateTimeSearch($subQ, $columns['datetime'], $searchTerm, $parsedDateTime);
                        }
                    }
                });
            });
        }
    }

    /**
     * Apply text search với support tiếng Việt
     */
    protected function applyTextSearch($query, array $columns, string $searchTerm)
    {
        foreach ($columns as $column) {
            $sqlExpr = $this->stringHandlerService->buildSqlUnaccentExpression($column);
            $query->orWhereRaw("LOWER($sqlExpr) LIKE ?", ["%{$searchTerm}%"]);
        }
    }

    /**
     * Apply date search (search bằng Y-m-d)
     */
    protected function applyDateSearch($query, array $columns, $searchDate)
    {
        foreach ($columns as $column) {
            $query->orWhereDate($column, $searchDate);
        }
    }

    /**
     * Apply date search cho trường hợp day/month (vd "18/10") trên các cột DATE (bất kỳ năm)
     */
    protected function applyDateDayMonthSearch($query, array $columns, $day, $month)
    {
        $formatted = sprintf('%02d/%02d', intval($day), intval($month));
        foreach ($columns as $column) {
            // Dùng DATE_FORMAT để so sánh 'dd/mm' — phù hợp cho cả DATE và DATETIME
            $query->orWhereRaw("DATE_FORMAT($column, '%d/%m') = ?", [$formatted]);
        }
    }

    /**
     * Apply date search cho trường hợp month/year (vd "10/2025")
     */
    protected function applyDateMonthYearSearch($query, array $columns, $month, $year)
    {
        $start = Carbon::create($year, $month, 1)->startOfDay()->format('Y-m-d H:i:s');
        $end = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');

        foreach ($columns as $column) {
            $query->orWhereBetween($column, [$start, $end]);
        }
    }

    /**
     * Apply date search cho trường hợp year-only (vd "2025")
     */
    protected function applyDateYearSearch($query, array $columns, $year)
    {
        $start = Carbon::create($year, 1, 1)->startOfDay()->format('Y-m-d H:i:s');
        $end = Carbon::create($year, 12, 31)->endOfDay()->format('Y-m-d H:i:s');

        foreach ($columns as $column) {
            $query->orWhereBetween($column, [$start, $end]);
        }
    }

    /**
     * Apply datetime search với nhiều format (mở rộng để match đầy đủ datetime + time-only + day/month + day/month time)
     */
    protected function applyDateTimeSearch($query, array $columns, string $searchTerm, $parsedDateTime)
    {
        foreach ($columns as $column) {
            if ($parsedDateTime) {
                switch ($parsedDateTime['type']) {
                    case 'full':
                        // full datetime (precision tới phút)
                        $query->orWhereBetween($column, [
                            $parsedDateTime['start'],
                            $parsedDateTime['end']
                        ]);
                        break;

                    case 'date':
                        $query->orWhereDate($column, $parsedDateTime['date']);
                        break;

                    case 'year':
                        $query->orWhereYear($column, $parsedDateTime['year']);
                        break;

                    case 'month':
                        $query->orWhere(function ($q) use ($column, $parsedDateTime) {
                            $q
                                ->whereYear($column, $parsedDateTime['year'])
                                ->whereMonth($column, $parsedDateTime['month']);
                        });
                        break;

                    case 'day_month':
                        // nhập "18/10" -> match day + month bất kỳ năm nào
                        // Dùng DATE_FORMAT để chắc chắn (hoạt động với DATE và DATETIME)
                        $formatted = sprintf('%02d/%02d', intval($parsedDateTime['day']), intval($parsedDateTime['month']));
                        $query->orWhereRaw("DATE_FORMAT($column, '%d/%m') = ?", [$formatted]);
                        break;

                    case 'time':
                        // nhập "09:16" hoặc "09:16:40" -> match time trong ngày (bất kỳ ngày)
                        $start = $parsedDateTime['time_start'];  // HH:MM:SS
                        $end = $parsedDateTime['time_end'];  // HH:MM:SS
                        $query->orWhereRaw("TIME($column) BETWEEN ? AND ?", [$start, $end]);
                        break;

                    case 'day_month_time':
                        // nhập "18/10 09:16" (không có năm) -> match day+month + time interval (bất kỳ năm)
                        $datePart = sprintf('%02d/%02d', intval($parsedDateTime['day']), intval($parsedDateTime['month']));
                        $timeStart = $parsedDateTime['time_start'];
                        $timeEnd = $parsedDateTime['time_end'];
                        $query->orWhere(function ($q) use ($column, $datePart, $timeStart, $timeEnd) {
                            $q
                                ->whereRaw("DATE_FORMAT($column, '%d/%m') = ?", [$datePart])
                                ->whereRaw("TIME($column) BETWEEN ? AND ?", [$timeStart, $timeEnd]);
                        });
                        break;
                }
            }

            // Fallback: search text trong datetime string (nếu user nhập dạng text)
            $query->orWhere($column, 'LIKE', "%{$searchTerm}%");
        }
    }
}
