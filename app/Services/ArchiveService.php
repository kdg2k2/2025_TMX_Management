<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kiwilan\Archive\Archive;
use Exception;

class ArchiveService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {}

    /**
     * Nén file/folder thành ZIP
     *
     * @param array $files - ['path/in/zip' => 'path/to/real/file']
     * @param string $zipPath - Đường dẫn file ZIP output (relative từ public)
     * @return string - Đường dẫn file ZIP đã tạo
     */
    public function compress(array $files, string $zipPath)
    {
        try {
            // Tách folder và filename
            $zipFolder = dirname($zipPath);
            $zipFileName = basename($zipPath);

            // Tạo folder trước
            $folderFullPath = $this->handlerUploadFileService->getAbsolutePublicPath($zipFolder);

            // Đường dẫn đầy đủ của file zip
            $zipFullPath = $folderFullPath . '/' . $zipFileName;

            $archive = Archive::make($zipFullPath);

            foreach ($files as $pathInArchive => $pathToRealFile) {
                $realPath = $this->handlerUploadFileService->getAbsolutePublicPath($pathToRealFile);

                if (is_dir($realPath)) {
                    $archive->addDirectory($pathInArchive, $realPath);
                } else {
                    $archive->addFile($pathInArchive, $realPath);
                }
            }

            $archive->save();

            Log::info("Nén file thành công: {$zipFullPath}");

            return $zipPath;
        } catch (Exception $e) {
            Log::error('Lỗi khi nén file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Nén từ string
     *
     * @param array $contents - ['filename.txt' => 'content string']
     * @param string $zipPath - Đường dẫn ZIP (relative từ public)
     * @return string
     */
    public function compressFromString(array $contents, string $zipPath)
    {
        try {
            $zipFullPath = $this->handlerUploadFileService->getAbsolutePublicPath($zipPath);

            $archive = Archive::make($zipFullPath);

            foreach ($contents as $filename => $content) {
                $archive->addFromString($filename, $content);
            }

            $archive->save();

            Log::info("Nén từ string thành công: {$zipFullPath}");

            return $zipPath;
        } catch (Exception $e) {
            Log::error('Lỗi khi nén từ string: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Đọc archive (.zip, .rar, .tar, .7z, .pdf, .epub, .cbz, .cbr, .cb7, .cbt)
     *
     * @param string $archivePath - Đường dẫn archive (relative từ public)
     * @param string $password - Mật khẩu nếu có
     * @return Archive
     */
    public function read(string $archivePath, string $password = null)
    {
        try {
            $fullPath = $this->handlerUploadFileService->getAbsolutePublicPath($archivePath);

            if (!file_exists($fullPath)) {
                throw new Exception('File archive không tồn tại');
            }

            return Archive::read($fullPath, $password);
        } catch (Exception $e) {
            Log::error('Lỗi khi đọc archive: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Giải nén archive
     *
     * @param string $archivePath - Đường dẫn archive (relative từ public)
     * @param string $extractFolder - Folder giải nén (relative từ public)
     * @param array $specificFiles - Chỉ giải nén những file cụ thể (ArchiveItem[])
     * @param string $password - Mật khẩu nếu có
     * @return array - Danh sách đường dẫn file đã giải nén
     */
    public function extract(string $archivePath, string $extractFolder, array $specificFiles = [], string $password = null)
    {
        try {
            $archive = $this->read($archivePath, $password);
            $extractPath = $this->handlerUploadFileService->getAbsolutePublicPath($extractFolder);

            if (!empty($specificFiles)) {
                $paths = $archive->extract($extractPath, $specificFiles);
            } else {
                $paths = $archive->extractAll($extractPath);
            }

            Log::info("Giải nén thành công: {$archivePath} -> {$extractFolder}");

            return $paths;
        } catch (Exception $e) {
            Log::error('Lỗi khi giải nén: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy danh sách file trong archive
     *
     * @param string $archivePath - Đường dẫn archive
     * @param string $password - Mật khẩu nếu có
     * @return array - ArchiveItem[]
     */
    public function listFiles(string $archivePath, string $password = null)
    {
        try {
            $archive = $this->read($archivePath, $password);
            return $archive->getFileItems();
        } catch (Exception $e) {
            Log::error('Lỗi khi liệt kê file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tìm file trong archive theo path
     *
     * @param string $archivePath - Đường dẫn archive
     * @param string $searchPath - Từ khóa tìm kiếm
     * @param string $password - Mật khẩu nếu có
     * @return mixed - ArchiveItem hoặc null
     */
    public function findFile(string $archivePath, string $searchPath, string $password = null)
    {
        try {
            $archive = $this->read($archivePath, $password);
            return $archive->find($searchPath);
        } catch (Exception $e) {
            Log::error('Lỗi khi tìm file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lọc file trong archive
     *
     * @param string $archivePath - Đường dẫn archive
     * @param string $filter - Từ khóa lọc (vd: 'jpeg', '.txt')
     * @param string $password - Mật khẩu nếu có
     * @return array - ArchiveItem[]
     */
    public function filterFiles(string $archivePath, string $filter, string $password = null)
    {
        try {
            $archive = $this->read($archivePath, $password);
            return $archive->filter($filter);
        } catch (Exception $e) {
            Log::error('Lỗi khi lọc file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy nội dung file từ archive
     *
     * @param string $archivePath - Đường dẫn archive
     * @param mixed $archiveItem - ArchiveItem cần lấy nội dung
     * @param string $password - Mật khẩu nếu có
     * @return string - Nội dung file
     */
    public function getFileContent(string $archivePath, $archiveItem, string $password = null)
    {
        try {
            $archive = $this->read($archivePath, $password);
            return $archive->getContents($archiveItem);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy nội dung file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy text từ file trong archive
     *
     * @param string $archivePath - Đường dẫn archive
     * @param mixed $archiveItem - ArchiveItem
     * @param string $password - Mật khẩu nếu có
     * @return string|null - Text content
     */
    public function getFileText(string $archivePath, $archiveItem, string $password = null)
    {
        try {
            $archive = $this->read($archivePath, $password);
            return $archive->getText($archiveItem);
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy text: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy thông tin stat của archive
     *
     * @param string $archivePath - Đường dẫn archive
     * @return object - Stat info
     */
    public function getArchiveStat(string $archivePath)
    {
        try {
            $archive = $this->read($archivePath);
            return $archive->stat();
        } catch (Exception $e) {
            Log::error('Lỗi khi lấy stat: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Đếm số file trong archive
     *
     * @param string $archivePath - Đường dẫn archive
     * @param string $password - Mật khẩu nếu có
     * @return int
     */
    public function countFiles(string $archivePath, string $password = null)
    {
        try {
            $archive = $this->read($archivePath, $password);
            return $archive->getCount();
        } catch (Exception $e) {
            Log::error('Lỗi khi đếm file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Đọc PDF metadata
     *
     * @param string $pdfPath - Đường dẫn PDF
     * @return object - PDF metadata
     */
    public function readPdfMetadata(string $pdfPath)
    {
        try {
            $archive = $this->read($pdfPath);
            return $archive->getPdf();
        } catch (Exception $e) {
            Log::error('Lỗi khi đọc PDF metadata: ' . $e->getMessage());
            throw $e;
        }
    }
}
