<?php
namespace App\Services;

class StringHandlerService
{
    protected function getAccentMap(): array
    {
        return [
            'à' => 'a', 'á' => 'a', 'ã' => 'a', 'ả' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẵ' => 'a', 'ẳ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẫ' => 'a', 'ẩ' => 'a', 'ậ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẽ' => 'e', 'ẻ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ễ' => 'e', 'ể' => 'e', 'ệ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ĩ' => 'i', 'ỉ' => 'i', 'ị' => 'i',
            'ò' => 'o', 'ó' => 'o', 'õ' => 'o', 'ỏ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ỗ' => 'o', 'ổ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ỡ' => 'o', 'ở' => 'o', 'ợ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ũ' => 'u', 'ủ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ữ' => 'u', 'ử' => 'u', 'ự' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỹ' => 'y', 'ỷ' => 'y', 'ỵ' => 'y',
            'đ' => 'd',
            'À' => 'A', 'Á' => 'A', 'Ã' => 'A', 'Ả' => 'A', 'Ạ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ẵ' => 'A', 'Ẳ' => 'A', 'Ặ' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ẫ' => 'A', 'Ẩ' => 'A', 'Ậ' => 'A',
            'È' => 'E', 'É' => 'E', 'Ẽ' => 'E', 'Ẻ' => 'E', 'Ẹ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ễ' => 'E', 'Ể' => 'E', 'Ệ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ĩ' => 'I', 'Ỉ' => 'I', 'Ị' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Õ' => 'O', 'Ỏ' => 'O', 'Ọ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ỗ' => 'O', 'Ổ' => 'O', 'Ộ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ỡ' => 'O', 'Ở' => 'O', 'Ợ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ũ' => 'U', 'Ủ' => 'U', 'Ụ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ữ' => 'U', 'Ử' => 'U', 'Ự' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỹ' => 'Y', 'Ỷ' => 'Y', 'Ỵ' => 'Y',
            'Đ' => 'D'
        ];
    }

    public function removeAccents(string $str): string
    {
        $map = $this->getAccentMap();
        return strtr($str, $map);
    }

    public function removeSpecialCharacters(string $str, string $separator = '-', bool $lower = true)
    {
        if ($lower)
            $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^a-z0-9ăâêôơưđ]+/u', $separator, $str);
        $str = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $str);
        $str = trim($str, $separator);
        return $str;
    }

    /**
     * Chuyển đổi chuỗi sang định dạng được chỉ định
     */
    public function formatCase(string $str, string $case = 'kebab'): string
    {
        // Tách chuỗi thành các từ
        $words = $this->splitIntoWords($str);

        switch (strtolower($case)) {
            case 'pascal':
                return $this->toPascalCase($words);
            case 'title':
                return $this->toTitleCase($words);
            case 'kebab':
                return $this->toKebabCase($words);
            case 'camel':
                return $this->toCamelCase($words);
            case 'snake':
                return $this->toSnakeCase($words);
            case 'upper_snake':
            case 'uppersnake':
            case 'constant':
                return $this->toUpperSnakeCase($words);
            case 'lower':
            case 'lowercase':
                return $this->toLowerCase($words);
            case 'upper':
            case 'uppercase':
                return $this->toUpperCase($words);
            default:
                return $this->toKebabCase($words);
        }
    }

    /**
     * Tách chuỗi thành mảng các từ
     */
    protected function splitIntoWords(string $str): array
    {
        // Loại bỏ dấu
        $str = $this->removeAccents($str);

        // Tách theo các ký tự đặc biệt, khoảng trắng, hoặc chuyển đổi từ hoa sang thường
        $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);  // camelCase -> camel Case
        $str = preg_replace('/[^a-zA-Z0-9]+/', ' ', $str);  // Thay ký tự đặc biệt bằng space

        // Tách thành mảng và loại bỏ phần tử rỗng
        return array_filter(array_map('trim', explode(' ', $str)));
    }

    /**
     * PascalCase: MoTaHopDong
     */
    protected function toPascalCase(array $words): string
    {
        return implode('', array_map('ucfirst', array_map('strtolower', $words)));
    }

    /**
     * Title Case: Mo Ta Hop Dong
     */
    protected function toTitleCase(array $words): string
    {
        return implode(' ', array_map('ucfirst', array_map('strtolower', $words)));
    }

    /**
     * kebab-case: mo-ta-hop-dong
     */
    protected function toKebabCase(array $words): string
    {
        return implode('-', array_map('strtolower', $words));
    }

    /**
     * camelCase: moTaHopDong
     */
    protected function toCamelCase(array $words): string
    {
        $result = array_map('ucfirst', array_map('strtolower', $words));
        $result[0] = strtolower($result[0]);
        return implode('', $result);
    }

    /**
     * snake_case: mo_ta_hop_dong
     */
    protected function toSnakeCase(array $words): string
    {
        return implode('_', array_map('strtolower', $words));
    }

    /**
     * lowercase: motahopdong
     */
    protected function toLowerCase(array $words): string
    {
        return strtolower(implode('', $words));
    }

    /**
     * UPPERCASE: MOTAHOPDONG
     */
    protected function toUpperCase(array $words): string
    {
        return strtoupper(implode('', $words));
    }

    protected function toUpperSnakeCase(array $words): string
    {
        $snakeCase = $this->toSnakeCase($words);
        return strtoupper($snakeCase);
    }

    /**
     * Cập nhật hàm createSlug để hỗ trợ format case
     */
    public function createSlug(string $str = null, string $separator = '-', string $format = null)
    {
        if ($str === null) {
            return '';
        }

        // Nếu có format, áp dụng format trước
        if ($format !== null) {
            return $this->formatCase($str, $format);
        }

        // Nếu không có format, giữ nguyên logic cũ
        $str = $this->removeAccents($str);
        return $this->removeSpecialCharacters($str, $separator);
    }

    public function buildSqlUnaccentExpression(string $column): string
    {
        $map = $this->getAccentMap();
        $expr = $column;
        foreach ($map as $from => $to) {
            $expr = "REPLACE($expr, " . $this->quoteSql($from) . ', ' . $this->quoteSql($to) . ')';
        }
        return $expr;
    }

    protected function quoteSql(string $s): string
    {
        $escaped = str_replace("'", "\'", $s);
        return "'{$escaped}'";
    }

    public function generateRandomString(string $prefix = '', int $length = 10)
    {
        $prefixLength = strlen($prefix);
        if ($prefixLength >= $length) {
            return substr($prefix, 0, $length);
        }
        $randomLength = $length - $prefixLength;
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        $bytes = random_bytes($randomLength);
        for ($i = 0; $i < $randomLength; $i++) {
            $randomString .= $characters[ord($bytes[$i]) % $charactersLength];
        }
        $timestamp = substr(str_replace('.', '', microtime(true)), -6);
        return $prefix . '_' . $timestamp . '_' . $randomString;
    }

    public function createPascalSlug(string $string)
    {
        return $this->createSlug($string, '', 'pascal');
    }

    public function createUpperSnakeCase(string $string)
    {
        return $this->createSlug($string, '', 'uppersnake');
    }
}
