<?php
namespace App\Services;

use App\Jobs\SendMailJob;
use App\Models\ProfessionalRecordMinute;
use Arr;
use Exception;
use \App\Repositories\ProfessionalRecordMinuteRepository;

class ProfessionalRecordMinuteService extends BaseService
{
    private $userService;
    private $systemConfigService;
    private $wordService;

    public function __construct()
    {
        $this->repository = app(ProfessionalRecordMinuteRepository::class);
        $this->userService = app(UserService::class);
        $this->systemConfigService = app(SystemConfigService::class);
        $this->wordService = app(WordService::class);
    }

    public function listExceptDraftSortByStatus(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $request = array_merge($request, [
                'except_status' => 'draft',
            ]);
            return $this->list($request);
        });
    }

    public function findByContractId(int $contractId)
    {
        return $this->tryThrow(function () use ($contractId) {
            return $this->repository->findByContractId($contractId);
        });
    }

    public function createPlanMinute(array $plan, $file)
    {
        return $this->tryThrow(function () use ($plan, $file) {
            // xóa các biên bản draft của gói thầu này trước
            $this->repository->deleteDraftByType($plan['id'], 'plan');

            $fileName = $file->getClientOriginalName();
            $folder = 'professional-record/minute/plan';
            $publicFolder = public_path($folder);
            if (!is_dir($publicFolder))
                mkdir($publicFolder, 0777, true);
            $file->move($folder, $fileName);

            $minute = $this->repository->store([
                'professional_record_plan_id' => $plan['id'],
                'type' => 'plan',
                'status' => 'draft',
                'path' => "$folder/$fileName",
            ]);

            return $minute;
        }, true);
    }

    public function createHandoverMinus(array $handover, bool $signed = false, ProfessionalRecordMinute $minute = null)
    {
        $plan = $handover['plan'];
        $contractMembers = app(ContractService::class)->getMembers(
            $plan['contract']['id']
        );
        $professionals = $contractMembers['professionals'] ?? [];
        if (!$this->isLocal())
            if (empty($professionals))
                throw new Exception('Hợp đồng chưa có phụ trách chuyên môn');

        $prepareData = $this->prepareDataToRenderMinuteFile($handover['details'], optional(reset($professionals))->id ?? $handover['user_id'], $handover['received_by'], $handover['user_id'], $signed);

        // xóa các biên bản draft của gói thầu này trước
        $this->repository->deleteDraftByType($handover['id'], 'handover');

        $templatePath = public_path('templates/handover_minute.docx');
        if (!file_exists($templatePath))
            throw new Exception('Template file not found: ' . $templatePath);

        $path = $this->renderMinuteFile($plan['contract']['name'], date('d/m/Y'), $prepareData, $templatePath, 'handover');

        if ($minute) {
            $minute->update([
                'path' => $path,
                'status' => 'approved',
            ]);
        } else {
            $minute = $this->repository->store([
                'professional_record_handover_id' => $handover['id'],
                'type' => 'handover',
                'status' => 'draft',
                'path' => $path,
            ]);
        }

        return $minute;
    }

    public function createUsageRegisterMinute(array $register, $file)
    {
        // xóa các biên bản draft của gói thầu này trước
        $this->repository->deleteDraftByType($register['id'], 'usage_register');

        $fileName = $file->getClientOriginalName();
        $folder = 'uploads/professional-record/minute/usage_register';
        $publicFolder = public_path($folder);
        if (!is_dir($publicFolder))
            mkdir($publicFolder, 0777, true);
        $file->move($folder, $fileName);

        $minute = $this->repository->store([
            'professional_record_usage_register_id' => $register['id'],
            'type' => 'usage_register',
            'status' => 'draft',
            'path' => "$folder/$fileName",
        ]);

        return $minute;
    }

    public function validateMinuteStatusWhenCreate(ProfessionalRecordMinute $minute = null)
    {
        if ($minute && !in_array($minute->status, ['draft', 'rejected']))
            throw new Exception('File biên bản đã được yêu cầu duyệt hoặc đã dược duyệt rồi!');
    }

    public function validateMinuteStatusWhenSendApproveRequest(ProfessionalRecordMinute $minute = null)
    {
        if ($minute && $minute->status != 'draft')
            throw new Exception('Biên bản ở trạng thái nháp mới có thể gửi yêu cầu duyệt!');
    }

    private function renderMinuteFile(string $contractName, string $handoverDate, array $data, string $fullPathTemplate, string $type)
    {
        $templateProcessor = $this->wordService->createFromTemplate($fullPathTemplate);

        $templateProcessor->setValue('tenCty', htmlspecialchars(mb_strtoupper(config('custom.DEFAULT_TITLE'), 'UTF-8')));
        $templateProcessor->setValue('tenhd', htmlspecialchars($contractName));
        $templateProcessor->setValue('ngaybangiao', htmlspecialchars($handoverDate));

        $templateProcessor->setValue('t_nhan', htmlspecialchars($data['bennhanUserInfo']['name']));
        $templateProcessor->setValue('p_nhan', htmlspecialchars($data['bennhanUserInfo']['department']['name']));
        $templateProcessor->setValue('t_giao', htmlspecialchars($data['bengiaoUserInfo']['name']));
        $templateProcessor->setValue('p_giao', htmlspecialchars($data['bengiaoUserInfo']['department']['name']));
        if ($data['nguoilapUserInfo'])
            $templateProcessor->setValue('t_nguoilap', htmlspecialchars($data['nguoilapUserInfo']['name']));

        $sl = count($data['table_1']);
        if ($sl > 0) {
            $stt = 1;
            $templateProcessor->cloneRow('c_tenhoso', $sl);
            foreach ($data['table_1'] as $value) {
                $templateProcessor->setValue('c_tt#' . $stt, $value['tt']);
                $templateProcessor->setValue('c_tenhoso#' . $stt, htmlspecialchars($value['ten']));
                $templateProcessor->setValue('c_dvt#' . $stt, $value['dvt']);
                $templateProcessor->setValue('c_soluong#' . $stt, (string) $value['soluong']);
                $stt++;
            }
        }

        if (!empty($data['bengiaoSign'])) {
            $templateProcessor->setImageValue('ck_bengiao', $data['bengiaoSign']);
        } else {
            $templateProcessor->setValue('ck_bengiao', '');
        }

        if (!empty($data['bennhanSign'])) {
            $templateProcessor->setImageValue('ck_bennhan', $data['bennhanSign']);
        } else {
            $templateProcessor->setValue('ck_bennhan', '');
        }

        if (!empty($data['nguoilapSign'])) {
            $templateProcessor->setImageValue('ck_nguoilap', $data['nguoilapSign']);
        } else {
            $templateProcessor->setValue('ck_nguoilap', '');
        }

        $sl_tg = count($data['table_2']);
        if ($sl_tg > 0) {
            $stt_tg = 1;
            $templateProcessor->cloneRow('tenhoso', $sl_tg);
            foreach ($data['table_2'] as $value) {
                $templateProcessor->setValue('tt#' . $stt_tg, $value['tt']);
                $templateProcessor->setValue('tenhoso#' . $stt_tg, htmlspecialchars($value['ten']));
                $templateProcessor->setValue('dvt#' . $stt_tg, $value['dvt']);
                $templateProcessor->setValue('sl#' . $stt_tg, (string) $value['soluong']);
                $templateProcessor->setValue('tinh#' . $stt_tg, $value['tinh']);
                $templateProcessor->setValue('xa#' . $stt_tg, $value['xa']);
                $templateProcessor->setValue('donvi#' . $stt_tg, $value['donvi']);
                $templateProcessor->setValue('ghichu#' . $stt_tg, $value['ghichu']);
                $stt_tg++;
            }
        }

        $folder = "uploads/professional-record/minute/$type";
        $des_pdf_file = public_path($folder);
        if (!is_dir($des_pdf_file))
            mkdir($des_pdf_file, 0777, true);

        $output = $folder . '/' . "professional_record_minute_{$type}_" . date('d-m-Y_H-i-s') . '.xlsx';;
        $docx = $output . '.docx';
        $pdf = $output . '.pdf';
        $templateProcessor->saveAs(public_path($docx));

        $convert = app(DocumentConversionService::class)->wordToPdf(public_path($docx), $des_pdf_file);
        if (empty($convert))
            throw new Exception('Lỗi chuyển đổi file biên bản từ word sang pdf!');

        return $pdf;
    }

    private function prepareDataToRenderMinuteFile(array $plantDetails, int $handoverById, int $receivedById, int $createrId = null, bool $signed = false)
    {
        $bengiaoUserInfo = $this->userService->findById($handoverById)->toArray();
        $bennhanUserInfo = $this->userService->findById($receivedById)->toArray();
        $nguoilapUserInfo = optional($this->userService->findById($createrId))->toArray();
        $bengiaoSign = $bennhanSign = $nguoilapSign = [];

        if ($signed == true) {
            $bengiaoSign = $bengiaoUserInfo['path_signature'];
            $bennhanSign = $bennhanUserInfo['path_signature'];
            if ($nguoilapUserInfo)
                $nguoilapSign = $nguoilapUserInfo['path_signature'];
        }

        $table_1 = array_map(
            fn($i, $row) => ['tt' => $i + 1] + $row,
            array_keys(
                $tmp = array_values(
                    array_reduce($plantDetails, function ($carry, $item) {
                        $key = data_get($item, 'type.name') . '|' . data_get($item, 'type.unit');
                        $carry[$key]['ten'] = data_get($item, 'type.name');
                        $carry[$key]['dvt'] = data_get($item, 'type.unit');
                        $carry[$key]['soluong'] = ($carry[$key]['soluong'] ?? 0) + (int) data_get($item, 'quantity', 0);
                        return $carry;
                    }, [])
                )
            ),
            $tmp
        );

        $table_2 = array_map(function ($item, $index) {
            return [
                'tt' => $index + 1,
                'ten' => data_get($item, 'type.name'),
                'dvt' => data_get($item, 'type.unit'),
                'soluong' => data_get($item, 'quantity'),
                'tinh' => data_get($item, 'province.name'),
                'xa' => data_get($item, 'commune.name'),
                'donvi' => data_get($item, 'unit.name'),
                'ghichu' => data_get($item, 'note'),
            ];
        }, $plantDetails, array_keys($plantDetails));

        return [
            'table_1' => $table_1,
            'table_2' => $table_2,
            'bengiaoSign' => $bengiaoSign,
            'bennhanSign' => $bennhanSign,
            'nguoilapSign' => $nguoilapSign,
            'bengiaoUserInfo' => $bengiaoUserInfo,
            'bennhanUserInfo' => $bennhanUserInfo,
            'nguoilapUserInfo' => $nguoilapUserInfo,
        ];
    }

    public function formatRecord(array $array)
    {
        if (empty($array))
            return $array;
        $array = parent::formatRecord($array);

        if (isset($array['type']))
            $array['type'] = $this->repository->getType($array['type']);

        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);

        if (isset($array['path']))
            $array['path'] = $this->getAssetUrl($array['path']);

        return $array;
    }

    private function getEmails($minute, $getContractMembers = true)
    {
        $baseRelations = [
            'user',
            'handoverBy',
            'receivedBy',
        ];
        $minute = $minute->load(
            $this->repository->relations
        );

        if ($minute->plan)
            $contract = $minute->plan->contract;
        elseif ($minute->handover)
            $contract = $minute->handover->plan->contract;
        elseif ($minute->usageRegister)
            $contract = $minute->usageRegister->plan->contract;

        $contractMemberIds = [];
        if ($getContractMembers) {
            $contractMember = app(ContractService::class)->getMembers($contract['id']);
            $contractMemberIds = collect(Arr::flatten([
                $contractMember['professionals'],
                $contractMember['disbursements'],
                $contractMember['instructors'],
            ]))->pluck('id')->unique()->toArray();
        }

        $emails = app(UserService::class)->getEmails(
            [
                $contractMemberIds,
                json_decode(app(SystemConfigService::class)->getProfessionalRecordUserSendEmailIds()->value),
            ]
        );

        foreach ([
            'plan',
            'handover',
            'usageRegister',
        ] as $relation) {
            if (isset($minute[$relation])) {
                foreach ($baseRelations as $baseRelation) {
                    if (isset($minute[$relation][$baseRelation]['email']))
                        $emails[] = $minute[$relation][$baseRelation]['email'];
                }
            }

            if (isset($minute[$relation]['approvedBy']) && isset($minute[$relation]['approvedBy']['email'])) {
                $emails[] = $minute[$relation]['approvedBy']['email'];
            }

            if (isset($minute['approvedByUser']) && isset($minute['approvedByUser']['email'])) {
                $emails[] = $minute['approvedByUser']['email'];
            }
        }

        return $emails;
    }

    public function sendMail(string $subject, array $data, ProfessionalRecordMinute $minute, bool $useJobQueue = true, bool $getContractMembers = true, bool $sendFile = true)
    {
        if (!isset($data['authUser']))
            $data['authUser'] = auth()->user()->name;

        $emails = $this->getEmails($minute, $getContractMembers);

        $view = 'emails.professional-record';
        $files = [public_path($minute['path'])];

        if ($useJobQueue) {
            SendMailJob::dispatch(
                $view,
                $subject,
                $emails,
                $data,
                $sendFile ? $files : []
            );
        } else {
            app(EmailService::class)->sendMail(
                $view,
                $subject,
                $emails,
                $data,
                $sendFile ? $files : []
            );
        }
    }

    public function accept(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $minute = $this->repository->findById($request['id'])->load($this->repository->relations);
            if (!$this->isLocal())
                if ($minute['status'] !== 'pending_approval')
                    throw new Exception('Biên bản không ở trạng thái chờ phê duyệt!');

            // cập nhật trạng thái biên bản
            $minute->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_note' => $request['approval_note'] ?? null,
            ]);

            switch ($minute['type']) {
                case 'plan':
                    $this->createProfessionalRecordHandoverWhenApprovePlan($minute);

                    $subject = 'Biên bản kế hoạch hồ sơ chuyên môn đã được phê duyệt';
                    $type = 1;
                    $contract = $minute->plan->contract;
                    $nguoilap = $minute->plan->user;
                    break;
                case 'handover':
                    $subject = 'Biên bản bàn giao hồ sơ chuyên môn đã được phê duyệt';
                    $type = 4;
                    $contract = $minute->handover->plan->contract;
                    $nguoilap = $minute->handover->user;

                    $minute = $this->createHandoverMinus($minute->handover->toArray(), true, $minute);
                    break;
                case 'usage_register':
                    $this->sendMail(
                        'Yêu cầu đăng ký sử dụng hồ sơ chuyên môn đã được phê duyệt',
                        [
                            'name' => $minute->usageRegister->plan->contract->name,
                            'type' => 7,
                            'lydo' => $request['approval_note'],
                            'nguoidangky' => $minute->usageRegister->registeredBy->name,
                            'ngaydangky' => $this->formatDateTimeForPreview($minute->usageRegister->created_at),
                        ],
                        $minute
                    );
                    return;
                default:
                    throw new Exception('Không xác định được loại biên bản');
            }

            $this->sendMail(
                $subject,
                [
                    'ngay_ycpheduyet' => $this->formatDateTimeForPreview($minute['updated_at']),
                    'contract' => $contract->load(app(ContractService::class)->repository->relations),
                    'name' => $contract->name,
                    'type' => $type,
                    'lydo' => $request['approval_note'],
                    'nguoilap' => $nguoilap,
                ],
                $minute
            );
        }, true);
    }

    private function createProfessionalRecordHandoverWhenApprovePlan(ProfessionalRecordMinute $minute)
    {
        // check xem kho còn đủ loại giấy tờ ko
        $planDetails = $this->controlProfessionalRecordTypeQuantity($minute);

        $professionalRecordHandoverService = app(ProfessionalRecordHandoverService::class);

        // xóa data bàn giao cũ của biên bản nếu có
        $professionalRecordHandoverService->deleteByPlanId($minute->plan->id);

        // tạo data bàn giao
        $handover = $professionalRecordHandoverService->store([
            'professional_record_plan_id' => $minute->plan->id,
            'user_id' => auth()->id(),
            'handover_by' => $minute->plan->handover_by,
            'received_by' => $minute->plan->received_by,
            'type' => 'out',
        ]);

        // tạo data detail bàn giao
        $handoverDetails = array_map(function ($item) use ($handover) {
            unset(
                $item['type'],
                $item['province'],
                $item['commune'],
                $item['unit'],
                $item['responsible_user'],
                $item['updated_at'],
                $item['created_at'],
                $item['id'],
                $item['professional_record_plan_id'],
                $item['estimated_time'],
                $item['responsible_user_id']
            );

            $item['professional_record_handover_id'] = $handover['id'];

            return $item;
        }, $planDetails->toArray());

        $handover->details()->createMany($handoverDetails);
    }

    public function controlProfessionalRecordTypeQuantity(ProfessionalRecordMinute $minute)
    {
        $planDetails = $minute->plan->details;

        $typeNeeds = collect($planDetails)
            ->groupBy('type.id')
            ->map(function ($g) {
                $type = $g->first()->type->toArray();
                return [
                    'type' => $type,
                    'needed' => $g->sum('quantity') - $type['quantity']
                ];
            })
            ->filter(fn($item) => $item['needed'] > 0)  // Chỉ lấy những cái thiếu
            ->values()
            ->toArray();

        // nếu kho không đủ số lượng thì báo lỗi
        if (!empty($typeNeeds)) {
            $message = ["Để duyệt biên bản cần thêm: \n"];
            $message = implode('', array_merge(
                $message,
                array_map(function ($item) {
                    return "{$item['needed']} {$item['type']['unit']} - {$item['type']['name']} \n";
                }, $typeNeeds)
            ));

            $this->sendMail(
                'Thiếu giấy tờ để duyệt biên bản kế hoạch',
                [
                    'type' => 9,
                    'name' => $minute->plan->contract->name,
                    'messages' => array_values(array_filter(
                        array_map('trim', explode("\n", trim($message))),
                        function ($line) {
                            return $line !== '';
                        }
                    ))
                ],
                $minute,
                false,
                false,
                false
            );

            throw new Exception($message);
        } else {
            // update lại số lượng kho
            $planDetails->each(function ($item) {
                $item->type->update([
                    'quantity' => $item->type->quantity - $item->quantity
                ]);
            });
        }

        $checkLimit = collect($planDetails)
            ->groupBy('type.id')
            ->map(function ($g) {
                $type = $g->first()->type->toArray();
                $needed = $type['quantity_limit'] - $type['quantity'];
                return [
                    'type' => $type,
                    'needed' => $needed
                ];
            })
            ->filter(fn($item) => $item['needed'] > 0)  // Chỉ lấy những cái thiếu
            ->values()
            ->toArray();
        // nếu kho đã có giấy tờ dưới số lượng tối thiểu thì báo mail bổ sung
        if (!empty($checkLimit)) {
            $message = implode('', array_map(function ($item) {
                return "{$item['needed']} {$item['type']['unit']} - {$item['type']['name']} \n";
            }, $checkLimit));

            $this->sendMail(
                'Yêu cầu bổ sung các loại giấy tờ hồ sơ chuyên môn sắp hết',
                [
                    'type' => 10,
                    'name' => $minute->plan->contract->name,
                    'messages' => array_values(array_filter(
                        array_map('trim', explode("\n", trim($message))),
                        function ($line) {
                            return $line !== '';
                        }
                    ))
                ],
                $minute,
                true,
                false,
                false
            );
        }

        return $planDetails;
    }

    public function deny(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $minute = $this->repository->findById($request['id']);
            if (!$this->isLocal())
                if ($minute['status'] !== 'pending_approval')
                    throw new Exception('Biên bản không ở trạng thái chờ phê duyệt!');

            $minute->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_note' => $request['rejection_note'] ?? null,
            ]);

            switch ($minute['type']) {
                case 'plan':
                    $subject = 'Biên bản kế hoạch chứng từ hồ sơ chuyên môn đã bị từ chối';
                    $type = 2;
                    $contract = $minute->plan->contract;
                    $nguoilap = $minute->plan->user;
                    break;
                case 'handover':
                    $subject = 'Biên bản bàn giao hồ sơ chuyên môn đã bị từ chối';
                    $type = 5;
                    $contract = $minute->handover->plan->contract;
                    $nguoilap = $minute->handover->user;
                    break;
                case 'usage_register':
                    $this->sendMail(
                        'Yêu cầu đăng ký sử dụng hồ sơ chuyên môn đã bị từ chối',
                        [
                            'name' => $minute->usageRegister->plan->contract->name,
                            'type' => 8,
                            'lydo' => $request['rejection_note'],
                            'nguoidangky' => $minute->usageRegister->registeredBy->name,
                            'ngaydangky' => $this->formatDateTimeForPreview($minute->usageRegister->created_at),
                        ],
                        $minute
                    );
                    return;
                default:
                    throw new Exception('Không xác định được loại biên bản');
            }

            $this->sendMail(
                $subject,
                [
                    'name' => $contract->name,
                    'type' => $type,
                    'lydo' => $request['rejection_note'],
                    'nguoilap' => $nguoilap,
                ],
                $minute
            );
        }, true);
    }
}
