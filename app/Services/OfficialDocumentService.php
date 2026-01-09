<?php

namespace App\Services;

use App\Repositories\OfficialDocumentRepository;

class OfficialDocumentService extends BaseService
{
    public function __construct(
        private UserService $userService,
        private OfficialDocumentTypeService $officialDocumentTypeService,
        private OfficialDocumentSectorService $officialDocumentSectorService,
        private ContractService $contractService,
        private IncomingOfficialDocumentService $incomingOfficialDocumentService,
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(OfficialDocumentRepository::class);
    }

    private $fileFields = [
        'pending_review_docx_file',
        'revision_docx_file',
        'comment_docx_file',
        'approve_docx_file',
        'released_pdf_file',
    ];

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        foreach ($this->fileFields as $item)
            if (isset($array[$item]))
                $array[$item] = $this->getAssetUrl($array[$item]);

        foreach ([
            'expected_release_date',
            'released_date',
        ] as $item)
            if (isset($array[$item]))
                $array[$item] = $this->formatDateForPreview($array[$item]);

        if (isset($array['program_type']))
            $array['program_type'] = $this->repository->getProgramType($array['program_type']);

        switch ($array['status']) {
            case 'pending_review':
                $array['tr_message'] = 'Chờ kiểm tra';
                $array['tr_color'] = 'primary';
                break;
            case 'reviewed':
                $array['tr_message'] = 'Chờ duyệt';
                $array['tr_color'] = 'danger';
                break;
            case 'approved':
                $array['tr_message'] = 'Chờ phát hành';
                $array['tr_color'] = 'success';
                break;

            default:
                break;
        }
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);

        if (isset($array['release_type']))
            $array['release_type'] = $this->repository->getReleaseType($array['release_type']);

        return $array;
    }

    public function getBaseDataForCEView(int $id = null)
    {
        $baseRequest = [
            'load_relations' => false,
            'columns' => [
                'id',
                'name'
            ]
        ];
        $res = [];
        $res['releaseTypes'] = $this->repository->getReleaseType();
        if ($id) {
            $res['data'] = $this->repository->findById($id);
            $res['releaseTypes'] = collect($res['releaseTypes'])->filter(fn($i) => $i['original'] != 'new')->toArray();
        }
        $res['authPosition'] = $this->getUser()->position_id;
        $res['programTypes'] = $this->repository->getProgramType();
        $res['users'] = $this->userService->list([
            ...$baseRequest,
            'columns' => [...$baseRequest['columns'], 'position_id'],
        ]);
        $res['reviewers'] = collect($res['users'])->filter(fn($i) => $i['position_id'] < 5)->values()->toArray();
        $res['officialDocumentTypes'] = $this->officialDocumentTypeService->list($baseRequest);
        $res['officialDocumentSectors'] = $this->officialDocumentSectorService->list($baseRequest);
        $res['contracts'] = $this->contractService->list($baseRequest);
        $res['incomingOfficialDocuments'] = $this->incomingOfficialDocumentService->list([
            'task_assignee_id' => $this->getUserId() != 1 ? $this->getUserId() : null
        ]);
        return $res;
    }

    protected function extractBefore(array $request)
    {
        $users = $request['users'];
        unset($request['users']);
        return [
            'request' => $request,
            'users' => $users,
        ];
    }

    protected function handleExtractAfter(array $extract, $data)
    {
        $data->users()->sync($extract['users']);
    }

    public function beforeStore(array $request)
    {
        $request['pending_review_docx_file'] = $this->handlerUploadFileService->storeAndRemoveOld($request['pending_review_docx_file'], $this->repository->model->getTable(), 'pending_review_docx_file');
        return $request;
    }

    protected function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles(collect($this->fileFields)->map(fn($i) => $entity[$i])->toArray());
    }

    public function reviewApprove(array $request)
    {
        return $this->tryThrow(function () use ($request) {}, true);
    }

    public function reviewReject(array $request)
    {
        return $this->tryThrow(function () use ($request) {}, true);
    }

    public function approve(array $request)
    {
        return $this->tryThrow(function () use ($request) {}, true);
    }

    public function reject(array $request)
    {
        return $this->tryThrow(function () use ($request) {}, true);
    }

    public function release(array $request)
    {
        return $this->tryThrow(function () use ($request) {}, true);
    }
}
