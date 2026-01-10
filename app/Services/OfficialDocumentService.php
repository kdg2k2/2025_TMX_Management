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

        if (isset($array['status'])) {
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
            $array['status'] = $this->repository->getStatus($array['status']);
        }

        if (isset($array['release_type']))
            $array['release_type'] = $this->repository->getReleaseType($array['release_type']);

        if (isset($array['incoming_official_document']))
            $array['incoming_official_document'] = $this->incomingOfficialDocumentService->formatRecord($array['incoming_official_document']);

        return $array;
    }

    public function getBaseDataForLCEView(int $id = null)
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
        $res['statuses'] = $this->repository->getStatus();
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

    protected function beforeStore(array $request)
    {
        return $this->storeFile($request, 'pending_review_docx_file');
    }

    private function storeFile(array $request, string $name)
    {
        $request[$name] = $this->handlerUploadFileService->storeAndRemoveOld($request[$name], $this->repository->model->getTable(), $name);
        return $request;
    }

    protected function afterStore($data, array $request)
    {
        $this->sendMail($data['id'], 'Đề nghị phê duyệt');
    }

    protected function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles(collect($this->fileFields)->map(fn($i) => $entity[$i])->toArray());
    }

    public function reviewApprove(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            if (isset($request['revision_docx_file']))
                $request = $this->storeFile($request, 'revision_docx_file');
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Chấp nhận kiểm tra', [$this->handlerUploadFileService->getAbsolutePublicPath($data['revision_docx_file'] ?? $data['pending_review_docx_file'])]);
            return $data;
        }, true);
    }

    public function reviewReject(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findById($request['id']);
            $oldReviewer = $data['reviewedBy']['name'];
            $data->update($request);
            $this->sendMail($data['id'], 'Từ chối kiểm tra', [$this->handlerUploadFileService->getAbsolutePublicPath($data['pending_review_docx_file'])], ['old_reviewer' => $oldReviewer]);
            return $data;
        }, true);
    }

    public function approve(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request = $this->storeFile($request, 'approve_docx_file');
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Phê duyệt', [$this->handlerUploadFileService->getAbsolutePublicPath($data['approve_docx_file'])]);
            return $data;
        }, true);
    }

    public function reject(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request = $this->storeFile($request, 'comment_docx_file');
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Từ chối', [
                $this->handlerUploadFileService->getAbsolutePublicPath($data['revision_docx_file'] ?? $data['pending_review_docx_file']),
                $this->handlerUploadFileService->getAbsolutePublicPath($data['comment_docx_file']),
            ]);
            return $data;
        }, true);
    }

    public function release(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request = $this->storeFile($request, 'released_pdf_file');
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Phát hành', [$this->handlerUploadFileService->getAbsolutePublicPath($data['released_pdf_file'])]);
            return $data;
        }, true);
    }

    private function sendMail(int $id, string $subject, array $files = [], array $mailData = [])
    {
        $data = $this->findById($id, true, false);
        $data = $this->formatRecord($data->load($this->repository->relations)->toArray());
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob('emails.official-document', "$subject công văn/quyết định ({$data['release_type']['converted']})", $emails, [
            'data' => $data,
            'mailData' => $mailData,
        ], $files));
    }

    private function getEmails(array $data)
    {
        return $this->userService->getEmails([
            $data['created_by']['id'],
            $data['reviewed_by']['id'] ?? null,
            $data['approved_by']['id'] ?? null,
            $data['signed_by']['id'] ?? null,
            $data['contract_id'] ? $this->contractService->getMemberEmails($data['contract_id'], [
                'professionals',
                'instructors',
                'disbursements',
            ]) : [],
            $data['incoming_official_document_id']
                ? $this->incomingOfficialDocumentService->getEmails(
                    $this->incomingOfficialDocumentService->formatRecord(
                        $this->incomingOfficialDocumentService->findById($data['incoming_official_document_id'])->toArray()
                    )
                )
                : null,
            $data['official_document_sector_id'] ? collect($data['official_document_sector']['users'])->pluck('id')->toArray() : [],
            collect($data['users'])->pluck('id')->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->getUserIdByScheduleKey('OFFICIAL_DOCUMENT')
        ]);
    }
}
