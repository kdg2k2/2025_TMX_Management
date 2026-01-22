<?php
namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;

class WordService extends BaseService
{
    public function createFromTemplate(string $fullPath)
    {
        return new TemplateProcessor($fullPath);
    }
}
