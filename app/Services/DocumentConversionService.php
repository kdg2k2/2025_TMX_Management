<?php
namespace App\Services;

use Log;

class DocumentConversionService
{
    public function wordToPdf($docxPath, $pdfOutput = null)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        try {
            $sofficeExe = $this->findLibreOfficeExecutable();

            if (!$sofficeExe) {
                Log::error('LibreOffice executable not found');
                return null;
            }

            $cmd = sprintf(
                '%s -env:UserInstallation=file:///C:/temp/libreoffice --headless --convert-to pdf --outdir %s %s',
                escapeshellarg($sofficeExe),
                escapeshellarg($pdfOutput),
                escapeshellarg($docxPath)
            );

            exec($cmd, $output, $return);

            if ($return == 0) {
                return [
                    'status' => 'Success',
                    'message' => 'Convert Success.'
                ];
            } else {
                Log::error('LibreOffice conversion failed', [
                    'command' => $cmd,
                    'output' => $output,
                    'return_code' => $return
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Conversion error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
