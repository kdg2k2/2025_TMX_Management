<?php

namespace App\Traits;

use App\Models\Contract;
use App\Models\ContractProductMinute;
use Exception;

trait ContractPermissionTraits
{
    /**
     * Kiểm tra xem user hiện tại có phải là chuyên môn hoặc giải ngân của hợp đồng không
     *
     * @param int $contractId ID của hợp đồng
     * @throws Exception Nếu user không có quyền
     */
    protected function checkIsProfessionalOrDisbursement(int $contractId): void
    {
        $userId = auth()->id();

        // Admin có thể làm mọi thứ
        if ($userId === 1) {
            return;
        }

        $contract = Contract::with(['professionals', 'disbursements'])
            ->find($contractId);

        if (!$contract) {
            throw new Exception('Không tìm thấy hợp đồng');
        }

        // Kiểm tra xem user có phải là chuyên môn của hợp đồng
        $isProfessional = $contract->professionals()
            ->where('user_id', $userId)
            ->exists();

        // Kiểm tra xem user có phải là giải ngân của hợp đồng
        $isDisbursement = $contract->disbursements()
            ->where('user_id', $userId)
            ->exists();

        if (!$isProfessional && !$isDisbursement) {
            throw new Exception('Bạn không có quyền thực hiện thao tác này. Chỉ chuyên môn hoặc giải ngân của hợp đồng mới được phép.');
        }
    }

    /**
     * Kiểm tra xem user hiện tại có phải là người kiểm tra của hợp đồng không
     *
     * @param int $contractId ID của hợp đồng
     * @throws Exception Nếu user không có quyền
     */
    protected function checkIsInspector(int $contractId): void
    {
        $userId = auth()->id();

        // Admin có thể làm mọi thứ
        if ($userId === 1) {
            return;
        }

        $contract = Contract::find($contractId);

        if (!$contract) {
            throw new Exception('Không tìm thấy hợp đồng');
        }

        // Kiểm tra xem user có phải là người kiểm tra của hợp đồng
        if ($contract->inspector_user_id !== $userId) {
            throw new Exception('Bạn không có quyền thực hiện thao tác này. Chỉ người kiểm tra của hợp đồng mới được phép.');
        }
    }

    /**
     * Kiểm tra quyền chấp nhận tồn tại sản phẩm
     *
     * Logic:
     * - Người hướng dẫn của hợp đồng được phép
     * - Trưởng bộ phận của người chuyên môn được phép
     * - Trường hợp đặc biệt: Nếu người chuyên môn là trưởng bộ phận VÀ không có người hướng dẫn
     *   thì người chuyên môn được phép
     *
     * @param int $minuteId ID của biên bản sản phẩm
     * @throws Exception Nếu user không có quyền
     */
    protected function checkCanConfirmIssues(int $minuteId): void
    {
        $userId = auth()->id();

        // Admin có thể làm mọi thứ
        if ($userId === 1) {
            return;
        }

        // Lấy thông tin biên bản với các relationship cần thiết
        $minute = ContractProductMinute::with([
            'contract.instructors',
            'professionalUser.department'
        ])->find($minuteId);

        if (!$minute) {
            throw new Exception('Không tìm thấy biên bản');
        }

        $contract = $minute->contract;
        $professionalUser = $minute->professionalUser;

        // 1. Kiểm tra xem user có phải là người hướng dẫn của hợp đồng không
        $instructorUserIds = $contract->instructors->pluck('user_id')->toArray();
        $isInstructor = in_array($userId, $instructorUserIds);

        if ($isInstructor) {
            return; // Người hướng dẫn được phép
        }

        // 2. Kiểm tra xem user có phải là trưởng bộ phận của người chuyên môn không
        if ($professionalUser && $professionalUser->department) {
            $departmentManagerId = $professionalUser->department->manager_id;

            // Chỉ kiểm tra nếu bộ phận có trưởng bộ phận (manager_id không null)
            if ($departmentManagerId !== null) {
                if ($departmentManagerId === $userId) {
                    return; // Trưởng bộ phận được phép
                }

                // 3. Trường hợp đặc biệt: Người chuyên môn là trưởng bộ phận và không có người hướng dẫn
                $hasInstructor = count($instructorUserIds) > 0;
                $isProfessionalDepartmentHead = $professionalUser->id === $departmentManagerId;

                if (!$hasInstructor && $isProfessionalDepartmentHead && $userId === $professionalUser->id) {
                    return; // Người chuyên môn là trưởng bộ phận và không có người hướng dẫn
                }
            }
        }

        throw new Exception('Bạn không có quyền chấp nhận tồn tại. Chỉ người hướng dẫn của hợp đồng, trưởng bộ phận của PT chuyên môn, hoặc PT chuyên môn (nếu là trưởng bộ phận và không có người hướng dẫn) mới được phép.');
    }
}
