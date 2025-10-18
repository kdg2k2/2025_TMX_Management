<?php
namespace App\Services;

use Exception;

class StringHandlerService
{
    public function stringToSlug(string $str, int $option = 0)
    {
        $from = '';
        $to = '';

        if (!in_array($option, [0, 1])) {
            throw new Exception('Invalid option for stringToSlug');
        }

        // Remove all marks (Latin alphabet)
        if ($option === 0) {
            $from = 'àáãảạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệđùúủũụưừứửữựòóỏõọôồốổỗộơờớởỡợìíỉĩịäëïîöüûñçýỳỹỵỷ';
            $to = 'aaaaaaaaaaaaaaaaaeeeeeeeeeeeduuuuuuuuuuuoooooooooooooooooiiiiiaeiiouuncyyyyy';
        }

        // Remove tone mark (Vietnamese alphabet)
        if ($option === 1) {
            $from = 'àáãảạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệđùúủũụưừứửữựòóỏõọôồốổỗộơờớởỡợìíỉĩịäëïîöüûñçýỳỹỵỷ';
            $to = 'aaaaaăăăăăăââââââeeeeeêêêêêêđuuuuuưưưưưưoooooôôôôôôơơơơơơiiiiiaeiiouuncyyyyy';
        }

        // Perform the character replacement
        $result = '';
        for ($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
            $char = mb_substr($str, $i, 1, 'UTF-8');
            $pos = mb_strpos($from, $char, 0, 'UTF-8');

            if ($pos !== false) {
                $result .= mb_substr($to, $pos, 1, 'UTF-8');
            } else {
                $result .= $char;
            }
        }

        return $result;
    }

    public function removeSpecialCharacters(string $str, string $separator = '-')
    {
        // Convert to lowercase
        $str = mb_strtolower($str, 'UTF-8');

        // Replace spaces and special characters with separator
        $str = preg_replace('/[^a-z0-9ăâêôơưđ]+/u', $separator, $str);

        // Remove multiple consecutive separators
        $str = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $str);

        // Remove separator from beginning and end
        $str = trim($str, $separator);

        return $str;
    }

    public function createSlug(string $str = null, int $option = 0, string $separator = '-')
    {
        // First, convert Vietnamese characters
        $str = $this->stringToSlug($str, $option);

        // Then remove special characters
        $str = $this->removeSpecialCharacters($str, $separator);

        return $str;
    }

    public function generateRandomString(string $prefix = '', int $length = 10)
    {
        $prefixLength = strlen($prefix);

        if ($prefixLength >= $length) {
            return substr($prefix, 0, $length);
        }

        $randomLength = $length - $prefixLength;

        // Định nghĩa bộ ký tự
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);
        $randomString = '';

        // Tạo random bytes và convert thành ký tự
        $bytes = random_bytes($randomLength);
        for ($i = 0; $i < $randomLength; $i++) {
            $randomString .= $characters[ord($bytes[$i]) % $charactersLength];
        }

        $timestamp = substr(str_replace('.', '', microtime(true)), -6);
        return $prefix . '_' . $timestamp . '_' . $randomString;
    }
}
