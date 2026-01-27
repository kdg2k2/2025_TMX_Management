<?php
namespace App\Services;

use COM;
use com_exception;
use Exception;
use Log;
use VARIANT;

class DocumentConversionService
{
    /**
     * Convert DOCX to PDF using MS Word COM with VARIANT
     *
     * @param string $docxPath - Đường dẫn file DOCX
     * @param string|null $pdfPathOrFolder - (Optional) Đường dẫn folder hoặc file PDF.
     *                                       Nếu null: lưu cùng thư mục với DOCX
     *                                       Nếu folder: tự động tạo tên file.pdf
     *                                       Nếu file path: dùng path đó
     * @param int $timeout - Timeout (giây)
     * @return bool|string - Trả về đường dẫn PDF nếu thành công, false nếu thất bại
     */
    public function convertDocxToPdf($docxPath, $pdfPathOrFolder = null, $timeout = 60)
    {
        $word = null;
        $doc = null;

        // Lưu timeout gốc
        $originalTimeLimit = ini_get('max_execution_time');

        try {
            if (!file_exists($docxPath)) {
                Log::error('DOCX not found', ['path' => $docxPath]);
                return false;
            }

            // Clean DOCX path
            $docxPath = str_replace('/', '\\', realpath($docxPath));

            // Xác định đường dẫn PDF
            $pdfPath = $this->determinePdfPath($docxPath, $pdfPathOrFolder);

            Log::info('Starting Word COM conversion', [
                'docx' => $docxPath,
                'pdf' => $pdfPath
            ]);

            // Set timeout tạm thời
            set_time_limit($timeout + 10);

            // Initialize Word
            $word = new COM('Word.Application');
            $word->Visible = false;
            $word->DisplayAlerts = false;

            // Open document
            $doc = $word->Documents->Open($docxPath);

            Log::info('Document opened, saving as PDF');

            // SaveAs with VARIANT
            $outputFile = new VARIANT($pdfPath, VT_BSTR);
            $fileFormat = new VARIANT(17, VT_I4);

            $doc->SaveAs($outputFile, $fileFormat);

            // Close
            $doc->Close(false);
            $doc = null;

            $word->Quit(false);
            $word = null;

            // Verify
            if (file_exists($pdfPath)) {
                Log::info('PDF created successfully', [
                    'path' => $pdfPath,
                    'size' => filesize($pdfPath)
                ]);
                return $pdfPath;
            }

            Log::error('PDF not created');
            return false;
        } catch (com_exception $e) {
            Log::error('COM Exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            $this->cleanup($doc, $word);
            return false;
        } catch (Exception $e) {
            Log::error('Exception', [
                'message' => $e->getMessage()
            ]);

            $this->cleanup($doc, $word);
            return false;
        } finally {
            // ALWAYS restore timeout (chạy dù có lỗi hay không)
            set_time_limit($originalTimeLimit);

            Log::info('Timeout restored', [
                'timeout' => $originalTimeLimit
            ]);
        }
    }

    /**
     * Xác định đường dẫn PDF output dựa trên input
     *
     * @param string $docxPath - Đường dẫn DOCX đầy đủ
     * @param string|null $pdfPathOrFolder - Folder, file path, hoặc null
     * @return string - Đường dẫn PDF đầy đủ
     */
    private function determinePdfPath($docxPath, $pdfPathOrFolder)
    {
        // Lấy tên file (không có extension)
        $docxBasename = pathinfo($docxPath, PATHINFO_FILENAME);

        // Case 1: Null hoặc empty -> lưu cùng thư mục với DOCX
        if (empty($pdfPathOrFolder)) {
            $pdfDir = dirname($docxPath);
            $pdfPath = $pdfDir . '\\' . $docxBasename . '.pdf';
        }
        // Case 2: Là một thư mục (folder)
        elseif (is_dir($pdfPathOrFolder)) {
            $pdfDir = realpath($pdfPathOrFolder);
            $pdfPath = $pdfDir . '\\' . $docxBasename . '.pdf';
        }
        // Case 3: Là đường dẫn file (có extension .pdf)
        elseif (pathinfo($pdfPathOrFolder, PATHINFO_EXTENSION) === 'pdf') {
            $pdfPath = $pdfPathOrFolder;

            // Tạo thư mục nếu chưa tồn tại
            $pdfDir = dirname($pdfPath);
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }

            // Convert to absolute path if needed
            if (!preg_match('/^[A-Z]:\\\\/i', $pdfPath)) {
                $pdfPath = realpath($pdfDir) . '\\' . basename($pdfPath);
            }
        }
        // Case 4: Là folder path chưa tồn tại -> tạo folder
        else {
            // Coi như là folder path
            if (!is_dir($pdfPathOrFolder)) {
                mkdir($pdfPathOrFolder, 0755, true);
            }
            $pdfDir = realpath($pdfPathOrFolder);
            $pdfPath = $pdfDir . '\\' . $docxBasename . '.pdf';
        }

        // Clean path
        $pdfPath = str_replace('/', '\\', $pdfPath);

        Log::info('PDF path determined', [
            'docx_name' => $docxBasename,
            'pdf_path' => $pdfPath
        ]);

        return $pdfPath;
    }

    /**
     * Convert with lock - tránh nhiều conversion đồng thời
     *
     * @return bool|string - Đường dẫn PDF nếu thành công, false nếu thất bại
     */
    public function convertWithLock($docxPath, $pdfPathOrFolder = null, $timeout = 60)
    {
        $lockFile = storage_path('app/word_conversion.lock');

        if (!is_dir(dirname($lockFile))) {
            mkdir(dirname($lockFile), 0755, true);
        }

        $lockHandle = fopen($lockFile, 'w');

        try {
            $locked = false;
            $waitTime = 0;
            $maxWait = 30;

            while (!$locked && $waitTime < $maxWait) {
                $locked = flock($lockHandle, LOCK_EX | LOCK_NB);
                if (!$locked) {
                    Log::info('Waiting for lock', ['waited' => $waitTime]);
                    sleep(1);
                    $waitTime++;
                }
            }

            if (!$locked) {
                Log::warning('Could not acquire lock after 30s');
                fclose($lockHandle);
                return false;
            }

            Log::info('Lock acquired, starting conversion');

            $result = $this->convertDocxToPdf($docxPath, $pdfPathOrFolder, $timeout);

            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);

            return $result;
        } catch (Exception $e) {
            if (isset($lockHandle)) {
                flock($lockHandle, LOCK_UN);
                fclose($lockHandle);
            }
            throw $e;
        }
    }

    /**
     * Convert with retry mechanism
     *
     * @return bool|string - Đường dẫn PDF nếu thành công, false nếu thất bại
     */
    public function convertWithRetry($docxPath, $pdfPathOrFolder = null, $maxRetries = 3, $timeout = 60)
    {
        for ($i = 1; $i <= $maxRetries; $i++) {
            Log::info("Conversion attempt {$i}/{$maxRetries}", ['file' => basename($docxPath)]);

            $result = $this->convertWithLock($docxPath, $pdfPathOrFolder, $timeout);

            if ($result !== false) {
                Log::info('Conversion succeeded', ['attempt' => $i, 'pdf' => $result]);
                return $result;
            }

            if ($i < $maxRetries) {
                Log::warning("Attempt {$i} failed, retrying in 2 seconds");
                sleep(2);
            }
        }

        Log::error('All conversion attempts failed', ['file' => $docxPath]);
        return false;
    }

    /**
     * Batch convert nhiều file
     *
     * @param array $files - Mảng các file config
     *                       Format 1: ['docx' => 'path/to/file.docx', 'pdf' => 'folder/or/file.pdf']
     *                       Format 2: ['docx' => 'path/to/file.docx'] (tự động lưu cùng thư mục)
     */
    public function batchConvert(array $files, $timeout = 60)
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        $total = count($files);

        foreach ($files as $index => $file) {
            $docxPath = $file['docx'] ?? null;
            $pdfPathOrFolder = $file['pdf'] ?? null;

            if (!$docxPath) {
                $results['failed'][] = [
                    'file' => $docxPath,
                    'error' => 'Invalid DOCX path'
                ];
                continue;
            }

            Log::info('Processing ' . ($index + 1) . "/{$total}: " . basename($docxPath));

            $pdfPath = $this->convertWithLock($docxPath, $pdfPathOrFolder, $timeout);

            if ($pdfPath !== false) {
                $results['success'][] = [
                    'docx' => $docxPath,
                    'pdf' => $pdfPath
                ];
            } else {
                $results['failed'][] = [
                    'file' => $docxPath,
                    'error' => 'Conversion failed'
                ];
            }

            // Small delay between conversions
            if ($index < $total - 1) {
                usleep(500000);  // 0.5 second
            }
        }

        Log::info('Batch conversion completed', [
            'total' => $total,
            'success' => count($results['success']),
            'failed' => count($results['failed'])
        ]);

        return $results;
    }

    /**
     * Cleanup COM objects
     */
    private function cleanup($doc, $word)
    {
        try {
            if ($doc) {
                $doc->Close(false);
                $doc = null;
            }
        } catch (Exception $e) {
            Log::warning('Cleanup doc error', ['message' => $e->getMessage()]);
        }

        try {
            if ($word) {
                $word->Quit(false);
                $word = null;
            }
        } catch (Exception $e) {
            Log::warning('Cleanup word error', ['message' => $e->getMessage()]);
        }
    }

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
