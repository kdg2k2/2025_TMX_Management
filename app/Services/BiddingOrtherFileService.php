<?php

namespace App\Services;

use App\Repositories\BiddingOrtherFileRepository;

class BiddingOrtherFileService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(BiddingOrtherFileRepository::class);
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $this->repository->insert(array_map(function ($item) use ($request) {
                return [
                    'created_by' => $request['created_by'],
                    'content' => $item['content'],
                    'bidding_id' => $item['bidding_id'],
                    'path' => $this->handlerUploadFileService->storeAndRemoveOld($item['path'], 'biddings', 'orther_file'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $request['orther_file']));
        }, true);
    }
}
