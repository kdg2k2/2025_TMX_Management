<?php
namespace App\Services;

use Log;

class DocumentConversionService
{
    public function wordToPdf(string $docxPath, ?string $pdfOutput = null): ?string
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        try {
            $sofficeExe = $this->findLibreOfficeExecutable();
            if (!$sofficeExe || !file_exists($docxPath)) {
                Log::error('LibreOffice not found or docx missing');
                return null;
            }

            $outDir = $pdfOutput
                ? rtrim($pdfOutput, '/\\')
                : dirname(realpath($docxPath));

            $cmd = sprintf(
                '%s -env:UserInstallation=file:///C:/temp/libreoffice --headless --convert-to pdf --outdir %s %s',
                escapeshellarg($sofficeExe),
                escapeshellarg($outDir),
                escapeshellarg($docxPath)
            );

            exec($cmd, $output, $return);

            if ($return !== 0) {
                Log::error('LibreOffice conversion failed', compact('cmd', 'output', 'return'));
                return null;
            }

            $pdfPath = $outDir . DIRECTORY_SEPARATOR
                . pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';

            return file_exists($pdfPath) ? $pdfPath : null;

        } catch (\Throwable $e) {
            Log::error('Conversion error', ['message' => $e->getMessage()]);
            return null;
        }
    }


    private function findLibreOfficeExecutable()
    {
        // Cache the executable path to avoid repeated searches
        static $cachedExecutable = null;

        if ($cachedExecutable !== null) {
            return $cachedExecutable;
        }

        // Method 1: Use 'where' command to find soffice.exe in PATH
        $output = [];
        $return = 0;
        exec('where soffice.exe 2>nul', $output, $return);

        if ($return === 0 && !empty($output[0])) {
            $cachedExecutable = trim($output[0]);
            Log::info('LibreOffice found using where command: ' . $cachedExecutable);
            return $cachedExecutable;
        }

        // Method 2: Fallback to hardcoded path
        $fallbackPath = 'C:/LibreOffice/program/soffice.exe';
        if (file_exists($fallbackPath)) {
            $cachedExecutable = $fallbackPath;
            Log::info('LibreOffice found in fallback path: ' . $cachedExecutable);
            return $cachedExecutable;
        }

        Log::error('LibreOffice executable not found in PATH or fallback location');
        return null;
    }
}
