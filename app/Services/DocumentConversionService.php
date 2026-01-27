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
     * Số conversions tối đa cùng lúc
     * Tùy chỉnh theo RAM VPS:
     * - 2GB RAM: maxConcurrent = 2
     * - 4GB RAM: maxConcurrent = 3-4
     * - 8GB RAM: maxConcurrent = 5-6
     */
    private $maxConcurrentConversions = 3;

    /**
     * Convert DOCX to PDF - Core method
     */
    public function convertDocxToPdf($docxPath, $pdfPathOrFolder = null, $timeout = 60)
    {
        $word = null;
        $doc = null;
        $originalTimeLimit = ini_get('max_execution_time');

        try {
            if (!file_exists($docxPath)) {
                Log::error('DOCX not found', ['path' => $docxPath]);
                return false;
            }

            $docxPath = str_replace('/', '\\', realpath($docxPath));
            $pdfPath = $this->determinePdfPath($docxPath, $pdfPathOrFolder);

            Log::info('Starting Word COM conversion', [
                'docx' => $docxPath,
                'pdf' => $pdfPath
            ]);

            set_time_limit($timeout + 10);

            $word = new COM('Word.Application');
            $word->Visible = false;
            $word->DisplayAlerts = false;

            $doc = $word->Documents->Open($docxPath);

            Log::info('Document opened, saving as PDF');

            $outputFile = new VARIANT($pdfPath, VT_BSTR);
            $fileFormat = new VARIANT(17, VT_I4);

            $doc->SaveAs($outputFile, $fileFormat);

            $doc->Close(false);
            $doc = null;

            $word->Quit(false);
            $word = null;

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
            set_time_limit($originalTimeLimit);
        }
    }

    /**
     * Convert với Semaphore - Giới hạn concurrent conversions
     *
     * @return array ['status' => 'success'|'busy'|'failed', 'pdf' => '...', 'message' => '...']
     */
    public function convertWithSemaphore($docxPath, $pdfPathOrFolder = null, $timeout = 60, $maxWait = 120)
    {
        $startTime = time();

        Log::info('Attempting conversion with semaphore', [
            'file' => basename($docxPath),
            'max_concurrent' => $this->maxConcurrentConversions
        ]);

        // Acquire semaphore slot
        $slot = $this->acquireSemaphoreSlot($docxPath, $maxWait);

        if ($slot === false) {
            $waited = time() - $startTime;

            Log::warning('Could not acquire semaphore slot', [
                'file' => basename($docxPath),
                'waited' => $waited,
                'max_wait' => $maxWait
            ]);

            return [
                'status' => 'busy',
                'message' => "Server is busy processing other conversions. Please try again later.",
                'waited' => $waited
            ];
        }

        Log::info('Semaphore slot acquired', [
            'slot' => $slot,
            'file' => basename($docxPath)
        ]);

        try {
            // Convert
            $result = $this->convertDocxToPdf($docxPath, $pdfPathOrFolder, $timeout);

            // Release slot
            $this->releaseSemaphoreSlot($slot);

            if ($result !== false) {
                return [
                    'status' => 'success',
                    'pdf' => $result
                ];
            } else {
                return [
                    'status' => 'failed',
                    'message' => 'Conversion failed'
                ];
            }

        } catch (Exception $e) {
            // Release slot ngay cả khi có lỗi
            $this->releaseSemaphoreSlot($slot);

            Log::error('Conversion exception', [
                'message' => $e->getMessage(),
                'slot' => $slot
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Acquire semaphore slot
     *
     * @return int|false - Slot number (1-N) hoặc false nếu không lấy được
     */
    private function acquireSemaphoreSlot($docxPath, $maxWait = 120)
    {
        $semaphoreDir = storage_path('app/semaphore');

        if (!is_dir($semaphoreDir)) {
            mkdir($semaphoreDir, 0755, true);
        }

        $waited = 0;

        while ($waited < $maxWait) {
            // Try to acquire any available slot
            for ($slot = 1; $slot <= $this->maxConcurrentConversions; $slot++) {
                $slotFile = $semaphoreDir . "/slot_{$slot}.lock";

                $handle = @fopen($slotFile, 'c+');
                if (!$handle) {
                    continue;
                }

                // Try non-blocking exclusive lock
                if (flock($handle, LOCK_EX | LOCK_NB)) {
                    // Got the slot!
                    // Write info to file
                    ftruncate($handle, 0);
                    fwrite($handle, json_encode([
                        'file' => basename($docxPath),
                        'pid' => getmypid(),
                        'started_at' => date('Y-m-d H:i:s')
                    ]));
                    fflush($handle);

                    // Store handle in static property để giữ lock
                    $this->storeLockHandle($slot, $handle);

                    Log::info('Acquired semaphore slot', [
                        'slot' => $slot,
                        'file' => basename($docxPath)
                    ]);

                    return $slot;
                }

                fclose($handle);
            }

            // No slot available, wait
            if ($waited % 10 === 0 && $waited > 0) {
                Log::info('Waiting for semaphore slot', [
                    'waited' => $waited,
                    'active_conversions' => $this->countActiveConversions()
                ]);
            }

            sleep(1);
            $waited++;
        }

        return false;
    }

    /**
     * Release semaphore slot
     */
    private function releaseSemaphoreSlot($slot)
    {
        $handle = $this->getLockHandle($slot);

        if ($handle) {
            flock($handle, LOCK_UN);
            fclose($handle);
            $this->removeLockHandle($slot);

            Log::info('Released semaphore slot', ['slot' => $slot]);
        }

        // Clean up slot file
        $slotFile = storage_path("app/semaphore/slot_{$slot}.lock");
        @unlink($slotFile);
    }

    /**
     * Count active conversions
     */
    private function countActiveConversions()
    {
        $semaphoreDir = storage_path('app/semaphore');

        if (!is_dir($semaphoreDir)) {
            return 0;
        }

        $count = 0;

        for ($slot = 1; $slot <= $this->maxConcurrentConversions; $slot++) {
            $slotFile = $semaphoreDir . "/slot_{$slot}.lock";

            if (!file_exists($slotFile)) {
                continue;
            }

            $handle = @fopen($slotFile, 'r');
            if (!$handle) {
                continue;
            }

            // Try to lock - nếu lock được nghĩa là slot đang rảnh
            if (flock($handle, LOCK_EX | LOCK_NB)) {
                flock($handle, LOCK_UN);
            } else {
                // Slot đang bị lock = đang convert
                $count++;
            }

            fclose($handle);
        }

        return $count;
    }

    /**
     * Store lock handles (để giữ lock không bị giải phóng)
     */
    private static $lockHandles = [];

    private function storeLockHandle($slot, $handle)
    {
        self::$lockHandles[$slot] = $handle;
    }

    private function getLockHandle($slot)
    {
        return self::$lockHandles[$slot] ?? null;
    }

    private function removeLockHandle($slot)
    {
        unset(self::$lockHandles[$slot]);
    }

    /**
     * Batch convert với semaphore
     */
    public function batchConvertParallel(array $files, $timeout = 60)
    {
        $results = [
            'success' => [],
            'failed' => [],
            'busy' => []
        ];

        $total = count($files);

        Log::info('Starting batch conversion', [
            'total' => $total,
            'max_concurrent' => $this->maxConcurrentConversions
        ]);

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

            Log::info("Processing " . ($index + 1) . "/{$total}: " . basename($docxPath));

            $result = $this->convertWithSemaphore($docxPath, $pdfPathOrFolder, $timeout, 60);

            if ($result['status'] === 'success') {
                $results['success'][] = [
                    'docx' => $docxPath,
                    'pdf' => $result['pdf']
                ];
            } elseif ($result['status'] === 'busy') {
                $results['busy'][] = [
                    'file' => $docxPath,
                    'message' => $result['message']
                ];
            } else {
                $results['failed'][] = [
                    'file' => $docxPath,
                    'error' => $result['message'] ?? 'Unknown error'
                ];
            }

            // Small delay
            usleep(100000); // 0.1s
        }

        Log::info('Batch conversion completed', [
            'total' => $total,
            'success' => count($results['success']),
            'failed' => count($results['failed']),
            'busy' => count($results['busy'])
        ]);

        return $results;
    }

    /**
     * Status check - Xem có bao nhiêu conversions đang chạy
     */
    public function getConversionStatus()
    {
        $active = $this->countActiveConversions();
        $available = $this->maxConcurrentConversions - $active;

        $semaphoreDir = storage_path('app/semaphore');
        $slots = [];

        if (is_dir($semaphoreDir)) {
            for ($slot = 1; $slot <= $this->maxConcurrentConversions; $slot++) {
                $slotFile = $semaphoreDir . "/slot_{$slot}.lock";

                if (file_exists($slotFile)) {
                    $handle = @fopen($slotFile, 'r');

                    if ($handle && !flock($handle, LOCK_EX | LOCK_NB)) {
                        // Slot đang bận
                        $content = fread($handle, 1024);
                        $info = json_decode($content, true);

                        $slots[$slot] = [
                            'status' => 'busy',
                            'file' => $info['file'] ?? 'unknown',
                            'started_at' => $info['started_at'] ?? 'unknown'
                        ];
                    } else {
                        $slots[$slot] = ['status' => 'available'];

                        if ($handle) {
                            flock($handle, LOCK_UN);
                        }
                    }

                    if ($handle) {
                        fclose($handle);
                    }
                } else {
                    $slots[$slot] = ['status' => 'available'];
                }
            }
        }

        return [
            'max_concurrent' => $this->maxConcurrentConversions,
            'active' => $active,
            'available' => $available,
            'slots' => $slots
        ];
    }

    /**
     * Kill hanging Word processes
     */
    public function killHangingWordProcesses()
    {
        try {
            exec('tasklist /FI "IMAGENAME eq WINWORD.EXE" /FO CSV', $output);

            $processCount = count($output) - 1;

            if ($processCount > 0) {
                Log::warning("Found {$processCount} Word processes, killing...");
                exec('taskkill /F /IM WINWORD.EXE /T');
                sleep(2);
                Log::info('Word processes killed');
            }

            return $processCount;

        } catch (Exception $e) {
            Log::error('Kill process error', ['message' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Cleanup stale semaphore locks
     */
    public function cleanupStaleLocks($maxAge = 600)
    {
        $semaphoreDir = storage_path('app/semaphore');

        if (!is_dir($semaphoreDir)) {
            return 0;
        }

        $cleaned = 0;

        for ($slot = 1; $slot <= $this->maxConcurrentConversions; $slot++) {
            $slotFile = $semaphoreDir . "/slot_{$slot}.lock";

            if (!file_exists($slotFile)) {
                continue;
            }

            // Check file age
            $age = time() - filemtime($slotFile);

            if ($age > $maxAge) {
                // Lock quá cũ, có thể bị stale
                $handle = @fopen($slotFile, 'r');

                if ($handle && flock($handle, LOCK_EX | LOCK_NB)) {
                    // Lock được = không ai đang dùng = stale lock
                    flock($handle, LOCK_UN);
                    fclose($handle);
                    @unlink($slotFile);

                    Log::warning('Cleaned stale lock', [
                        'slot' => $slot,
                        'age' => $age
                    ]);

                    $cleaned++;
                } elseif ($handle) {
                    fclose($handle);
                }
            }
        }

        return $cleaned;
    }

    /**
     * Determine PDF path từ input
     */
    private function determinePdfPath($docxPath, $pdfPathOrFolder)
    {
        $docxBasename = pathinfo($docxPath, PATHINFO_FILENAME);

        if (empty($pdfPathOrFolder)) {
            $pdfDir = dirname($docxPath);
            $pdfPath = $pdfDir . '\\' . $docxBasename . '.pdf';
        }
        elseif (is_dir($pdfPathOrFolder)) {
            $pdfDir = realpath($pdfPathOrFolder);
            $pdfPath = $pdfDir . '\\' . $docxBasename . '.pdf';
        }
        elseif (pathinfo($pdfPathOrFolder, PATHINFO_EXTENSION) === 'pdf') {
            $pdfPath = $pdfPathOrFolder;

            $pdfDir = dirname($pdfPath);
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }

            if (!preg_match('/^[A-Z]:\\\\/i', $pdfPath)) {
                $pdfPath = realpath($pdfDir) . '\\' . basename($pdfPath);
            }
        }
        else {
            if (!is_dir($pdfPathOrFolder)) {
                mkdir($pdfPathOrFolder, 0755, true);
            }
            $pdfDir = realpath($pdfPathOrFolder);
            $pdfPath = $pdfDir . '\\' . $docxBasename . '.pdf';
        }

        $pdfPath = str_replace('/', '\\', $pdfPath);

        return $pdfPath;
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
        } catch (Exception $e) {}

        try {
            if ($word) {
                $word->Quit(false);
                $word = null;
            }
        } catch (Exception $e) {}
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

