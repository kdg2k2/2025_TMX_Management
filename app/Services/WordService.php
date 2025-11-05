<?php
namespace App\Services;

class WordService extends BaseService
{
    public function createFromTemplate(string $fullPath)
    {
        return new \PhpOffice\PhpWord\TemplateProcessor($fullPath);
    }
}
