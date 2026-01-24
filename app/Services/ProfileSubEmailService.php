<?php

namespace App\Services;

class ProfileSubEmailService extends UserSubEmailService
{
    /**
     * Tự động thêm user_id từ auth trước khi store
     */
    protected function beforeStore(array $request)
    {
        $request['user_id'] = $this->getUserId();
        return $request;
    }

    /**
     * Check ownership trước khi update
     */
    protected function beforeUpdate(array $request)
    {
        $this->checkOwnership($request['id']);
        return $request;
    }

    /**
     * Check ownership trước khi delete
     */
    protected function beforeDelete(int $id)
    {
        $email = $this->findById($id);
        $this->checkOwnership($id);
        return $email;
    }

    /**
     * Override beforeListQuery để tự động filter theo user_id
     */
    protected function beforeListQuery(array $request)
    {
        $request['user_id'] = $this->getUserId();
        return $request;
    }

    /**
     * Kiểm tra quyền sở hữu email
     *
     * @param int $id
     * @throws \Exception
     */
    public function checkOwnership(int $id): void
    {
        $email = $this->findById($id);

        if ($email->user_id !== $this->getUserId()) {
            throw new \Exception('Bạn không có quyền thực hiện thao tác này với email này', 403);
        }
    }

    /**
     * Tìm email theo ID và check ownership
     *
     * @param int $id
     * @param bool $loadRelation
     * @param bool $returnFormatRecord
     * @return mixed
     */
    public function findByIdWithOwnershipCheck(int $id, bool $loadRelation = true, bool $returnFormatRecord = false)
    {
        $this->checkOwnership($id);
        return $this->findById($id, $loadRelation, $returnFormatRecord);
    }
}
