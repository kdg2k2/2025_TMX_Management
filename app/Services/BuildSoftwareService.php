<?php

namespace App\Services;

use App\Models\BuildSoftware;
use App\Repositories\BuildSoftwareRepository;
use Illuminate\Support\Arr;

class BuildSoftwareService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
        private ContractService $contractService,
        private UserService $userService
    ) {
        $this->repository = app(BuildSoftwareRepository::class);
    }

    public function getListBaseData()
    {
        return [
            'status' => $this->repository->getStatus(),
            'state' => $this->repository->getState(),
            'developmentCases' => $this->repository->getDevelopmentCase(),
        ];
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id);

        $res['users'] = $this->userService->list([
            'columns' => [
                'id',
                'name',
            ],
            'load_relations' => false,
        ]);
        $res['contracts'] = $this->contractService->list([
            'columns' => [
                'id',
                'name',
            ],
            'load_relations' => false,
        ]);

        return array_merge($res, $this->getListBaseData());
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->store($request);
            $this->handleFileAndRelation($data, $extracted);
            $this->sendMail($data['id'], 'Yêu cầu xây dựng phần mềm');
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->update($request);
            $this->handleFileAndRelation($data, $extracted, true);
            $this->sendMail($data['id'], 'Cập nhật yêu cầu xây dựng phần mềm');
        }, true);
    }

    private function extractFields(array &$request): array
    {
        $fields = [
            'attachment',
            'business_analysts',
            'members',
        ];

        $extracted = [];
        foreach ($fields as $field) {
            $extracted[$field] = $request[$field] ?? null;
            unset($request[$field]);
        }

        return $extracted;
    }

    private function handleFileAndRelation(BuildSoftware $data, array $extracted, bool $isUpdate = false)
    {
        if ($extracted['attachment']) {
            $oldFile = $isUpdate ? $data['attachment'] : null;
            $data['attachment'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['attachment'], $this->repository->getTable(), 'attachment', $oldFile);
            $data->save();
        }

        $this->businessAnalysts($data, $extracted['business_analysts'] ?? []);
        $this->contractMembers($data, $extracted['members'] ?? []);
    }

    private function businessAnalysts(BuildSoftware $data, array $ids)
    {
        $this->syncRelationship($data, 'build_software_id', 'businessAnalysts', array_map(fn($i) => ['user_id' => $i], $ids));
    }

    private function contractMembers(BuildSoftware $data, array $ids)
    {
        $this->syncRelationship($data, 'build_software_id', 'members', array_map(fn($i) => ['user_id' => $i], $ids));
    }

    protected function beforeDelete(int $id)
    {
        $data = parent::beforeDelete($id);
        $this->sendMail($data['id'], 'Hủy yêu cầu xây dựng phần mềm');
        return $data;
    }

    protected function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles($entity['attachment'] ?? null);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        $array['state'] = $this->repository->getState($array['state']);
        $array['status'] = $this->repository->getStatus($array['status']);
        $array['development_case'] = $this->repository->getDevelopmentCase($array['development_case']);
        if (isset($array['attachment']))
            $array['attachment'] = $this->getAssetUrl($array['attachment']);
        if (isset($array['rejected_at']))
            $array['rejected_at'] = $this->formatDateTimeForPreview($array['rejected_at']);
        if (isset($array['accepted_at']))
            $array['accepted_at'] = $this->formatDateTimeForPreview($array['accepted_at']);
        if (isset($array['completed_at']))
            $array['completed_at'] = $this->formatDateTimeForPreview($array['completed_at']);
        if (isset($array['deadline']))
            $array['deadline'] = $this->formatDateForPreview($array['deadline']);
        if (isset($array['start_date']))
            $array['start_date'] = $this->formatDateForPreview($array['start_date']);
        return $array;
    }

    public function accept(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Phê duyệt yêu cầu');
        }, true);
    }

    public function reject(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Tư chối yêu cầu', [
                'rejectionReason' => $request['rejection_reason'] ?? '',
            ]);
        }, true);
    }

    public function updateState(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request['start_date'] = null;
            if ($request['state'] != 'pending')
                $request['start_date'] = date('Y-m-d');
            if ($request['state'] == 'completed')
                $request['completed_at'] = date('Y-m-d H:i:s');

            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Cập nhật tiến trình');
        }, true);
    }

    private function getEmails(BuildSoftware $data)
    {
        $contractEmails = [];
        if ($data['contract_id'])
            $contractEmails = $this->contractService->getMemberEmails($data['contract_id'], [
                'executor_user',
                'instructors',
                'professionals',
            ]);

        $recordMemberEmails = $this->userService->getEmails(array_merge(
            $this->userService->getUserDepartmentManagerEmail($data['createdBy']['id'] ?? null), [
                $data['verifyBy']['id'] ?? null,
                $data['createdBy']['id'] ?? null,
            ]
        ));

        return Arr::flatten(array_merge($contractEmails,
            $recordMemberEmails));
    }

    private function sendMail(int $id, string $subject, array $params = [])
    {
        $record = $this->repository->findById($id);
        $emails = $this->getEmails($record);
        $files = $record['attachment'] ? [public_path($record['attachment'])] : [];
        $data = array_merge([
            'data' => $this->formatRecord($record->toArray()),
        ], $params);
        dispatch(new \App\Jobs\SendMailJob('emails.build-software', $subject . ' xây dựng phần mềm', $emails, $data, $files));
    }
}
