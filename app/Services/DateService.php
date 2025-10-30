<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class DateService
{
    public function __construct(
        private StringHandlerService $stringHandlerService
    ) {}

    /**
     * Lấy danh sách các ngày trong khoảng from -> to
     * Method cơ bản - các method khác sẽ kế thừa logic từ đây
     *
     * @param string|Carbon $from Ngày bắt đầu
     * @param string|Carbon $to Ngày kết thúc
     * @param array $ignoreDays Mảng các ngày cần bỏ qua (0=CN, 1=T2, ..., 6=T7)
     * @param int|null $month Tháng cần filter (1-12)
     * @param int|null $year Năm cần filter
     * @return Collection<Carbon>
     */
    protected function getFilteredCarbonDates(
        string|Carbon $from,
        string|Carbon $to,
        array $ignoreDays = [],
        ?int $month = null,
        ?int $year = null
    ): Collection {
        $startDate = $this->parseDate($from, false);  // false = return Carbon
        $endDate = $this->parseDate($to, false);  // false = return Carbon

        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = collect();

        foreach ($period as $date) {
            // Bỏ qua nếu là ngày trong danh sách ignore
            if (in_array($date->dayOfWeek, $ignoreDays)) {
                continue;
            }

            // Filter theo tháng/năm nếu có
            if ($month !== null && $date->month !== $month) {
                continue;
            }

            if ($year !== null && $date->year !== $year) {
                continue;
            }

            $dates->push($date->copy());
        }

        return $dates;
    }

    /**
     * Lấy danh sách các ngày trong khoảng from -> to (string format)
     *
     * @param string|Carbon $from Ngày bắt đầu
     * @param string|Carbon $to Ngày kết thúc
     * @param array $ignoreDays Mảng các ngày cần bỏ qua (0=CN, 1=T2, ..., 6=T7)
     * @param string $format Format trả về (default: 'Y-m-d')
     * @param int|null $month Tháng cần filter (1-12)
     * @param int|null $year Năm cần filter
     * @return Collection<string>
     */
    public function getDatesInRange(
        string|Carbon $from,
        string|Carbon $to,
        array $ignoreDays = [],
        string $format = 'Y-m-d',
        ?int $month = null,
        ?int $year = null
    ): Collection {
        return $this
            ->getFilteredCarbonDates($from, $to, $ignoreDays, $month, $year)
            ->map(fn(Carbon $date) => $date->format($format));
    }

    /**
     * Lấy danh sách các ngày dưới dạng Carbon instances
     *
     * @param string|Carbon $from
     * @param string|Carbon $to
     * @param array $ignoreDays
     * @param int|null $month
     * @param int|null $year
     * @return Collection<Carbon>
     */
    public function getCarbonDatesInRange(
        string|Carbon $from,
        string|Carbon $to,
        array $ignoreDays = [],
        ?int $month = null,
        ?int $year = null
    ): Collection {
        return $this->getFilteredCarbonDates($from, $to, $ignoreDays, $month, $year);
    }

    /**
     * Lấy tổng số ngày từ from -> to
     * Sử dụng getFilteredCarbonDates để kế thừa logic
     *
     * @param string|Carbon $from Ngày bắt đầu
     * @param string|Carbon $to Ngày kết thúc
     * @param array $ignoreDays Mảng các ngày cần bỏ qua (0=CN, 1=T2, ..., 6=T7)
     * @param int|null $month Tháng cần filter (1-12)
     * @param int|null $year Năm cần filter
     * @return int
     */
    public function getTotalDays(
        string|Carbon $from,
        string|Carbon $to,
        array $ignoreDays = [],
        ?int $month = null,
        ?int $year = null
    ): int {
        // Kế thừa logic từ getFilteredCarbonDates
        return $this->getFilteredCarbonDates($from, $to, $ignoreDays, $month, $year)->count();
    }

    /**
     * Lấy tổng số ngày dưới dạng decimal (0.5 = nửa ngày, 1 = cả ngày)
     *
     * @param string|Carbon $from Ngày bắt đầu
     * @param string|Carbon $to Ngày kết thúc
     * @param string $fromSession Buổi bắt đầu ('morning' hoặc 'afternoon')
     * @param string $toSession Buổi kết thúc ('morning' hoặc 'afternoon')
     * @param array $ignoreDays Mảng các ngày cần bỏ qua
     * @param int|null $month Tháng cần filter (1-12)
     * @param int|null $year Năm cần filter
     * @return float
     */
    public function getTotalDaysDecimal(
        string|Carbon $from,
        string|Carbon $to,
        string $fromSession = 'morning',
        string $toSession = 'afternoon',
        array $ignoreDays = [],
        ?int $month = null,
        ?int $year = null
    ): float {
        $startDate = $from instanceof Carbon ? $from->copy() : Carbon::parse($from);
        $endDate = $to instanceof Carbon ? $to->copy() : Carbon::parse($to);

        // Nếu cùng ngày
        if ($startDate->isSameDay($endDate)) {
            // Kiểm tra có trong ignore list không
            if (in_array($startDate->dayOfWeek, $ignoreDays)) {
                return 0;
            }

            // Kiểm tra filter tháng/năm
            if ($month !== null && $startDate->month !== $month) {
                return 0;
            }
            if ($year !== null && $startDate->year !== $year) {
                return 0;
            }

            if ($fromSession === 'morning' && $toSession === 'afternoon') {
                return 1.0;  // Cả ngày
            }
            return 0.5;  // Nửa ngày
        }

        $totalDays = 0.0;

        // Tính ngày đầu tiên
        if (!in_array($startDate->dayOfWeek, $ignoreDays)) {
            $includeFirstDay = true;
            if ($month !== null && $startDate->month !== $month) {
                $includeFirstDay = false;
            }
            if ($year !== null && $startDate->year !== $year) {
                $includeFirstDay = false;
            }

            if ($includeFirstDay) {
                $totalDays += $fromSession === 'morning' ? 1.0 : 0.5;
            }
        }

        // Tính các ngày ở giữa (sử dụng getFilteredCarbonDates để kế thừa logic)
        if ($startDate->diffInDays($endDate) > 1) {
            $middleStart = $startDate->copy()->addDay();
            $middleEnd = $endDate->copy()->subDay();

            $middleDays = $this->getFilteredCarbonDates($middleStart, $middleEnd, $ignoreDays, $month, $year)->count();
            $totalDays += $middleDays * 1.0;
        }

        // Tính ngày cuối cùng
        if (!in_array($endDate->dayOfWeek, $ignoreDays)) {
            $includeLastDay = true;
            if ($month !== null && $endDate->month !== $month) {
                $includeLastDay = false;
            }
            if ($year !== null && $endDate->year !== $year) {
                $includeLastDay = false;
            }

            if ($includeLastDay) {
                $totalDays += $toSession === 'afternoon' ? 1.0 : 0.5;
            }
        }

        return $totalDays;
    }

    /**
     * Lấy tổng số buổi (sáng/chiều) từ from -> to
     * Sử dụng getTotalDays để kế thừa logic
     *
     * @param string|Carbon $from Ngày bắt đầu
     * @param string|Carbon $to Ngày kết thúc
     * @param array $ignoreDays Mảng các ngày cần bỏ qua (0=CN, 1=T2, ..., 6=T7)
     * @param int|null $month Tháng cần filter (1-12)
     * @param int|null $year Năm cần filter
     * @return int
     */
    public function getTotalSessions(
        string|Carbon $from,
        string|Carbon $to,
        array $ignoreDays = [],
        ?int $month = null,
        ?int $year = null
    ): int {
        // Kế thừa logic từ getTotalDays
        $totalDays = $this->getTotalDays($from, $to, $ignoreDays, $month, $year);
        return $totalDays * 2;
    }

    /**
     * Lấy tổng số buổi với tùy chọn buổi bắt đầu và kết thúc
     * Sử dụng getTotalDaysDecimal để kế thừa logic
     *
     * @param string|Carbon $from Ngày bắt đầu
     * @param string|Carbon $to Ngày kết thúc
     * @param string $fromSession Buổi bắt đầu ('morning' hoặc 'afternoon')
     * @param string $toSession Buổi kết thúc ('morning' hoặc 'afternoon')
     * @param array $ignoreDays Mảng các ngày cần bỏ qua
     * @param int|null $month Tháng cần filter (1-12)
     * @param int|null $year Năm cần filter
     * @return float
     */
    public function getTotalSessionsDetailed(
        string|Carbon $from,
        string|Carbon $to,
        string $fromSession = 'morning',
        string $toSession = 'afternoon',
        array $ignoreDays = [],
        ?int $month = null,
        ?int $year = null
    ): float {
        // Kế thừa logic từ getTotalDaysDecimal
        $totalDaysDecimal = $this->getTotalDaysDecimal($from, $to, $fromSession, $toSession, $ignoreDays, $month, $year);
        return $totalDaysDecimal * 2;
    }

    /**
     * Lấy số ngày làm việc (T2-T6) trong khoảng from -> to
     * Sử dụng getTotalDays để kế thừa logic
     *
     * @param string|Carbon $from
     * @param string|Carbon $to
     * @param int|null $month
     * @param int|null $year
     * @return int
     */
    public function getWorkingDays(
        string|Carbon $from,
        string|Carbon $to,
        ?int $month = null,
        ?int $year = null
    ): int {
        // Kế thừa logic từ getTotalDays, bỏ qua thứ 7 (6) và Chủ nhật (0)
        return $this->getTotalDays($from, $to, [0, 6], $month, $year);
    }

    /**
     * Lấy số buổi làm việc (T2-T6) trong khoảng from -> to
     * Sử dụng getTotalSessions để kế thừa logic
     *
     * @param string|Carbon $from
     * @param string|Carbon $to
     * @param int|null $month
     * @param int|null $year
     * @return int
     */
    public function getWorkingSessions(
        string|Carbon $from,
        string|Carbon $to,
        ?int $month = null,
        ?int $year = null
    ): int {
        // Kế thừa logic từ getTotalSessions
        return $this->getTotalSessions($from, $to, [0, 6], $month, $year);
    }

    /**
     * Kiểm tra một ngày có nằm trong danh sách ignore không
     *
     * @param string|Carbon $date
     * @param array $ignoreDays
     * @return bool
     */
    public function isIgnoredDay(string|Carbon $date, array $ignoreDays = []): bool
    {
        $checkDate = $this->parseDate($date);
        return in_array($checkDate->dayOfWeek, $ignoreDays);
    }

    /**
     * Kiểm tra một ngày có match với filter tháng/năm không
     *
     * @param string|Carbon $date
     * @param int|null $month
     * @param int|null $year
     * @return bool
     */
    public function matchesMonthYear(string|Carbon $date, ?int $month = null, ?int $year = null): bool
    {
        $checkDate = $this->parseDate($date);

        if ($month !== null && $checkDate->month !== $month) {
            return false;
        }

        if ($year !== null && $checkDate->year !== $year) {
            return false;
        }

        return true;
    }

    /**
     * Lấy tên ngày trong tuần bằng tiếng Việt
     *
     * @param int $dayOfWeek (0=CN, 1=T2, ..., 6=T7)
     * @return string
     */
    public function getDayName(int $dayOfWeek): string
    {
        $days = [
            0 => 'Chủ nhật',
            1 => 'Thứ hai',
            2 => 'Thứ ba',
            3 => 'Thứ tư',
            4 => 'Thứ năm',
            5 => 'Thứ sáu',
            6 => 'Thứ bảy',
        ];

        return $days[$dayOfWeek] ?? '';
    }

    /**
     * Lấy danh sách các ngày trong tuần bị ignore dưới dạng text
     *
     * @param array $ignoreDays
     * @return Collection
     */
    public function getIgnoreDayNames(array $ignoreDays): Collection
    {
        return collect($ignoreDays)->map(fn($day) => $this->getDayName($day));
    }

    /**
     * Parse tháng/năm từ string format 'MM/YYYY' hoặc 'YYYY-MM'
     *
     * @param string $monthYear Format: '09/2025' hoặc '2025-09'
     * @return array{month: int, year: int}
     */
    public function parseMonthYear(string $monthYear): array
    {
        // Sử dụng parseDateTime để xử lý
        $parsed = $this->parseDateTime($monthYear);

        if ($parsed && $parsed['type'] === 'month') {
            return [
                'month' => (int) $parsed['month'],
                'year' => (int) $parsed['year'],
            ];
        }

        // Fallback: parse thủ công
        if (str_contains($monthYear, '/')) {
            [$month, $year] = explode('/', $monthYear);
        } else {
            [$year, $month] = explode('-', $monthYear);
        }

        return [
            'month' => (int) $month,
            'year' => (int) $year,
        ];
    }

    /**
     * Format tháng/năm thành string
     *
     * @param int $month
     * @param int $year
     * @param string $format 'slash' => '09/2025', 'dash' => '2025-09'
     * @return string
     */
    public function formatMonthYear(int $month, int $year, string $format = 'slash'): string
    {
        $monthStr = str_pad((string) $month, 2, '0', STR_PAD_LEFT);

        return $format === 'slash'
            ? "{$monthStr}/{$year}"
            : "{$year}-{$monthStr}";
    }

    /**
     * Parse datetime với nhiều format, mở rộng nhận diện time-only và day/month + time
     */
    public function parseDateTime($dateString)
    {
        $dateString = trim($dateString);

        // Check nếu chỉ là năm (4 số)
        if (preg_match('/^\d{4}$/', $dateString)) {
            return [
                'type' => 'year',
                'year' => $dateString
            ];
        }

        // Check nếu là time-only: HH:mm hoặc HH:mm:ss
        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $dateString, $m)) {
            $h = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $i = $m[2];
            $s = isset($m[3]) ? str_pad($m[3], 2, '0', STR_PAD_LEFT) : '00';

            return [
                'type' => 'time',
                'time_start' => "{$h}:{$i}:{$s}",
                'time_end' => "{$h}:{$i}:" . ($s === '00' ? '59' : $s)  // nếu có giây, match chính xác giây; nếu không, match tới :59
            ];
        }

        // Check nếu là day/month + time (ví dụ "18/10 09:16" hoặc "18-10 09:16:30" hoặc "18.10 09:16")
        if (preg_match('/^(\d{1,2})[\/\.\-](\d{1,2})[\sT]+(\d{1,2}:\d{2}(?::\d{2})?)$/', $dateString, $m)) {
            $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);

            // parse time part
            if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $m[3], $t)) {
                $h = str_pad($t[1], 2, '0', STR_PAD_LEFT);
                $i = $t[2];
                $s = isset($t[3]) ? str_pad($t[3], 2, '0', STR_PAD_LEFT) : '00';
                return [
                    'type' => 'day_month_time',
                    'day' => $day,
                    'month' => $month,
                    'time_start' => "{$h}:{$i}:{$s}",
                    'time_end' => "{$h}:{$i}:" . ($s === '00' ? '59' : $s)
                ];
            }
        }

        // Check nếu là day/month (ví dụ "18/10" hoặc "18-10" hoặc "18.10")
        if (preg_match('/^(\d{1,2})[\/\.\-](\d{1,2})$/', $dateString, $matches)) {
            return [
                'type' => 'day_month',
                'day' => str_pad($matches[1], 2, '0', STR_PAD_LEFT),
                'month' => str_pad($matches[2], 2, '0', STR_PAD_LEFT)
            ];
        }

        // Check nếu là tháng/năm (ví dụ "10/2025" hoặc "10-2025")
        if (preg_match('/^(\d{1,2})[\/\-](\d{4})$/', $dateString, $matches)) {
            return [
                'type' => 'month',
                'month' => str_pad($matches[1], 2, '0', STR_PAD_LEFT),
                'year' => $matches[2]
            ];
        }

        // Parse full datetime bằng các format phổ biến
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'd/m/Y H:i:s',
            'd/m/Y H:i',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date) {
                    return [
                        'type' => 'full',
                        'start' => $date->copy()->startOfMinute()->format('Y-m-d H:i:s'),
                        'end' => $date->copy()->endOfMinute()->format('Y-m-d H:i:s')
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Nếu chỉ parse được date
        $parsedDate = $this->parseDate($dateString);
        if ($parsedDate) {
            return [
                'type' => 'date',
                'date' => $parsedDate
            ];
        }

        return null;
    }

    /**
     * Parse date
     */
    public function parseDate(string|Carbon $dateString, bool $returnString = true): string|Carbon|null
    {
        // Nếu đã là Carbon instance
        if ($dateString instanceof Carbon) {
            return $returnString ? $dateString->format('Y-m-d') : $dateString->copy();
        }

        $dateString = trim($dateString);

        // Thử parse với Carbon::parse() trước (linh hoạt và an toàn nhất)
        try {
            $parsed = Carbon::parse($dateString);
            return $returnString ? $parsed->format('Y-m-d') : $parsed;
        } catch (\Exception $e) {
            // Nếu thất bại, thử các format cụ thể
        }

        $formats = [
            'Y-m-d',
            'd-m-Y',
            'd/m/Y',
            'Y/m/d',
            'd.m.Y',
            'Y.m.d'
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date && $date->format($format) === $dateString) {
                    return $returnString ? $date->format('Y-m-d') : $date;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Lấy danh sách các tuần trong năm kèm đánh dấu tuần hiện tại
     * @param int $year Năm cần lấy danh sách tuần
     * @return array Mảng các tuần với thông tin week_number, start_date, end_date, is_current
     */
    public function getWeeksOfYear(int $year): array
    {
        $currentWeek = Carbon::now()->weekOfYear;
        $currentYear = (int) Carbon::now()->year;

        // Lấy ngày đầu tiên của năm
        $firstDay = Carbon::createFromDate($year, 1, 1);

        // Tính số tuần trong năm (52 hoặc 53 tuần)
        $totalWeeks = $firstDay->weeksInYear();

        $weeks = [];
        for ($weekNumber = 1; $weekNumber <= $totalWeeks; $weekNumber++) {
            // Lấy ngày đầu tuần (Thứ 2) và cuối tuần (Chủ nhật)
            $startOfWeek = Carbon::now()->setISODate($year, $weekNumber, 1);  // 1 = Thứ 2
            $endOfWeek = Carbon::now()->setISODate($year, $weekNumber, 7);  // 7 = Chủ nhật

            $weeks[] = [
                'week_number' => $weekNumber,
                'start_date' => $startOfWeek->format('Y-m-d'),
                'end_date' => $endOfWeek->format('Y-m-d'),
                'label' => "Tuần {$weekNumber} ({$startOfWeek->format('d/m/Y')} - {$endOfWeek->format('d/m/Y')})",
                'is_current' => ($weekNumber === $currentWeek && $year === $currentYear),
            ];
        }

        return $weeks;
    }

    /**
     * Lấy tuần hiện tại từ string date
     * @param string $date Format: 'Y-m-d' hoặc bất kỳ format hợp lệ
     * @return int Số tuần trong năm (1-53)
     */
    public function getWeekFromDate(string $date): int
    {
        return (int) Carbon::parse($date)->weekOfYear;
    }

    /**
     * Lấy các ngày trong tuần theo số tuần và năm
     * @param int $week Số tuần (1-53)
     * @param int $year Năm
     * @return array Mảng các ngày với thông tin thứ, sorted từ T2->CN
     */
    public function getDaysInWeek(int $week, int $year): array
    {
        $firstDayOfWeek = Carbon::now()
            ->setISODate($year, $week, 1);  // 1 = Thứ 2

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $firstDayOfWeek->copy()->addDays($i);
            $days[] = [
                'date' => $date->format('Y-m-d'),
                'day_of_week' => $date->dayOfWeek,  // 0=CN, 1=T2, ..., 6=T7
                'day_name' => $this->stringHandlerService->formatCase($date->locale('vi')->dayName, 'title', false),  // Tên tiếng Việt
                'day_name_short' => $date->locale('vi')->shortDayName,  // Thứ 2, Thứ 3...
            ];
        }

        return $days;
    }
}
