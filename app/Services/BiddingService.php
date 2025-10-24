<?php

namespace App\Services;

use App\Repositories\BiddingRepository;

class BiddingService extends BaseService
{
    public function __construct(
        private ArchiveService $archiveService,
        private StringHandlerService $stringHandlerService
    ) {
        $this->repository = app(BiddingRepository::class);
    }

    public function getShowBaseData(int $id)
    {
        return [
            'data' => $this->repository->findById($id),
            'biddingContractorExperienceFileTypes' => app(BiddingContractorExperienceService::class)->getFileType(),
            'biddingimplementationPersonnelJobtitles' => app(BiddingimplementationPersonnelService::class)->getJobTitle(),
        ];
    }

    public function downloadBuiltResult(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            $data = $this->repository->findById($id)->toArray();
            $biddingImplementationPersonnel = collect($data['bidding_implementation_personnel'] ?? [])
                ->pluck('files.*.personel_file.path')
                ->flatten()
                ->toArray();
            $biddingEligibility = collect($data['bidding_eligibility'] ?? [])->pluck('eligibility.path')->filter()->toArray();
            $biddingOrtherFile = array_column($data['bidding_orther_file'] ?? [], 'path');
            $biddingProofContract = collect($data['bidding_proof_contract'] ?? [])->pluck('proof_contract.path')->filter()->toArray();
            $biddingSoftwareOwnership = collect($data['bidding_software_ownership'] ?? [])->pluck('software_ownership.path')->filter()->toArray();
            $biddingContractorExperience = collect($data['bidding_contractor_experience'] ?? [])->map(function ($item) {
                $fileType = $item['file_type'];
                return $item['contract'][$fileType] ?? null;
            })->filter()->toArray();

            $zips = [
                'bidding-implementation-personnel' => $biddingImplementationPersonnel,
                'bidding-eligibility' => $biddingEligibility,
                'bidding-orther-file' => $biddingOrtherFile,
                'bidding-proof-contract' => $biddingProofContract,
                'bidding-software-ownership' => $biddingSoftwareOwnership,
                'bidding-contractor-experience' => $biddingContractorExperience,
            ];

            $fileHandler = app(\App\Services\HandlerUploadFileService::class);
            $tempFolder = 'uploads/biddings/temp-zips/' . uniqid();
            $outputFolder = 'uploads/biddings/download-built-result';

            $fileHandler->getAbsolutePublicPath($tempFolder);
            $fileHandler->getAbsolutePublicPath($outputFolder);

            $childZipPaths = [];

            // Bước 1: Tạo từng file zip con
            foreach ($zips as $zipName => $files) {
                // Lọc file tồn tại
                $existingFiles = array_filter($files, function ($file) use ($fileHandler) {
                    $fullPath = $fileHandler->getAbsolutePublicPath($file);
                    return file_exists($fullPath);
                });

                // Bỏ qua nếu không có file nào
                if (empty($existingFiles)) {
                    continue;
                }

                // Chuẩn bị files để nén (key = path trong zip, value = path thực tế)
                $filesToCompress = [];
                foreach ($existingFiles as $index => $file) {
                    $fileName = basename($file);
                    $filesToCompress[$fileName] = $file;
                }

                // Tạo file zip con
                $childZipPath = "$tempFolder/$zipName.zip";
                $this->archiveService->compress($filesToCompress, $childZipPath);
                $childZipPaths[] = $childZipPath;
            }

            // Bước 2: Tạo file zip tổng từ các file zip con
            if (empty($childZipPaths)) {
                throw new \Exception('Không có file nào để nén');
            }

            $finalZipName = $this->stringHandlerService->createSlug($data['name']);
            $finalZipPath = "$outputFolder/$finalZipName.zip";

            $filesToCompressFinal = [];
            foreach ($childZipPaths as $childZip) {
                $zipFileName = basename($childZip);
                $filesToCompressFinal[$zipFileName] = $childZip;
            }

            $this->archiveService->compress($filesToCompressFinal, $finalZipPath);

            // Bước 3: Xóa các file zip con và folder temp
            foreach ($childZipPaths as $childZip) {
                $fileHandler->removeFiles($childZip);
            }
            $fileHandler->removeFolder($fileHandler->getAbsolutePublicPath($tempFolder));

            return [
                'success' => true,
                'path' => $finalZipPath,
                'download_url' => asset($finalZipPath),
            ];
        });
    }
}
