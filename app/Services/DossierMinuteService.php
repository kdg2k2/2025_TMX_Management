<?php
namespace App\Services;

use App\Jobs\SendMailJob;
use App\Models\DossierMinute;
use Exception;
use \App\Repositories\DossierMinuteRepository;

class DossierMinuteService extends BaseService
{
    private $userService;
    private $systemConfigService;
    private $personnelFileService;
    private $wordService;

    public function __construct()
    {
        $this->repository = app(DossierMinuteRepository::class);
        $this->userService = app(UserService::class);
        $this->systemConfigService = app(SystemConfigService::class);
        $this->personnelFileService = app(PersonnelFileService::class);
        $this->wordService = app(WordService::class);
    }

    public function findByContractId(int $contractId)
    {
        return $this->tryThrow(function () use ($contractId) {
            return $this->repository->findByContractId($contractId);
        });
    }

    public function createPlanMinute(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $dossierPlanService = app(DossierPlanService::class);
            $plan = $dossierPlanService->findByIdContractAndYear($request['contract_id'], null);
            if (empty($plan))
                throw new Exception('Không tồn tại dữ liệu kế hoạch');

            $lastMinute = $dossierPlanService->getLastMinute($dossierPlanService->findById($plan['id']));
            $this->validateMinuteStatusWhenCreate($lastMinute);

            $plan = $dossierPlanService->findById($plan['id']);
            $plan->update([
                'handover_by' => $this->systemConfigService->getDossierPlanHandoverId()->value,
                'received_by' => $request['received_by'],
                'handover_date' => $request['handover_date'],
                'user_id' => auth()->id(),
            ]);

            $prepareData = $this->prepareDataToRenderMinuteFile($plan->details->toArray(), $plan['handover_by'], $plan['received_by'], auth()->id());

            // xóa các biên bản draft của gói thầu này trước
            $this->repository->deleteDraftByType($plan['id'], 'plan');

            $templatePath = public_path('dossier/templates/dossier_plan_minute.docx');
            if (!file_exists($templatePath))
                throw new Exception('Template file not found: ' . $templatePath);

            $path = $this->renderMinuteFile($plan['contract']['name'], $this->formatDateForPreview($plan['handover_date']), $prepareData, $templatePath, 'plan');

            $minute = $this->repository->store([
                'dossier_plan_id' => $plan['id'],
                'type' => 'plan',
                'status' => 'draft',
                'path' => $path,
            ]);
            return $minute;
        }, true);
    }

    public function createHandoverMinus(array $handover, bool $signed = false, DossierMinute $minute = null)
    {
        $plan = $handover['plan'];
        $contractMembers = app(ContractService::class)->getMemberInContract(
            app(ContractService::class)->findById($plan['contract']['id'])
        );
        $chuyenmon = $contractMembers['chuyenmon'] ?? [];
        if (!$this->isLocal())
            if (empty($chuyenmon))
                throw new Exception('Hợp đồng chưa có phụ trách chuyên môn');

        $prepareData = $this->prepareDataToRenderMinuteFile($handover['details'], optional(reset($chuyenmon))->id ?? $handover['user_id'], $handover['received_by'], $handover['user_id'], $signed);

        // xóa các biên bản draft của gói thầu này trước
        $this->repository->deleteDraftByType($handover['id'], 'handover');

        $templatePath = public_path('dossier/templates/dossier_handover_minute.docx');
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
                'dossier_handover_id' => $handover['id'],
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
        $folder = 'dossier/minute/usage_register';
        $publicFolder = public_path($folder);
        if (!is_dir($publicFolder))
            mkdir($publicFolder, 0777, true);
        $file->move($folder, $fileName);

        $minute = $this->repository->store([
            'dossier_usage_register_id' => $register['id'],
            'type' => 'usage_register',
            'status' => 'draft',
            'path' => "$folder/$fileName",
        ]);

        return $minute;
    }

    public function validateMinuteStatusWhenCreate(DossierMinute $minute = null)
    {
        if ($minute && !in_array($minute->status, ['draft', 'rejected']))
            throw new Exception('File biên bản đã được yêu cầu duyệt hoặc đã dược duyệt rồi!');
    }

    public function validateMinuteStatusWhenSendApproveRequest(DossierMinute $minute = null)
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

        $folder = "dossier/minute/$type";
        $des_pdf_file = public_path($folder);
        if (!is_dir($des_pdf_file))
            mkdir($des_pdf_file, 0777, true);

        $output = $folder . '/' . uniqid("dossier_minute_$type");
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
        // dd(
        //     $plantDetails, $handoverById, $receivedById, $createrId, $signed
        // );
        $bengiaoUserInfo = $this->userService->findById($handoverById)->toArray();
        $bennhanUserInfo = $this->userService->findById($receivedById)->toArray();
        $nguoilapUserInfo = optional($this->userService->findById($createrId))->toArray();
        $bengiaoSign = $bennhanSign = $nguoilapSign = [];

        if ($signed == true) {
            $bengiaoSign = $this->personnelFileService->getSignature($bengiaoUserInfo['name'], null);
            $bennhanSign = $this->personnelFileService->getSignature($bennhanUserInfo['name'], null);
            if ($nguoilapUserInfo)
                $nguoilapSign = $this->personnelFileService->getSignature($nguoilapUserInfo['name'], $nguoilapUserInfo['vitri_ifee'] ?? null);
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
        $baseRelations = $this->repository->baseRelation;
        $minute = $minute->load(
            $this->repository->relations
        );

        if ($minute->plan)
            $contract = $minute->plan->contract;
        elseif ($minute->handover)
            $contract = $minute->handover->plan->contract;
        elseif ($minute->usageRegister)
            $contract = $minute->usageRegister->plan->contract;

        $contractMemberEmails = [];
        if ($getContractMembers) {
            $contractMember = app(ContractService::class)->getMemberInContract($contract);
            $contractMemberEmails = app(UserService::class)->getUserEmails([
                $contractMember['chuyenmon'],
                $contractMember['giaingan'],
                $contractMember['huongdan'],
            ]);
        }

        $emailSchedule = app(TaskScheduleService::class)->findById(36);
        $emails = array_merge($contractMemberEmails, array_map('trim', explode(', ', $emailSchedule->emails)));

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

    public function sendMail(string $subject, array $data, DossierMinute $minute, bool $useJobQueue = true, bool $getContractMembers = true)
    {
        if (!isset($data['authUser']))
            $data['authUser'] = auth()->user()->name;

        $emails = $this->getEmails($minute, $getContractMembers);

        $view = 'sendMail.sendGDD';
        $files = [public_path($minute['path'])];

        if ($useJobQueue) {
            SendMailJob::dispatch(
                $view,
                $subject,
                $emails,
                $data,
                $files
            );
        } else {
            app(EmailService::class)->sendMail(
                $view,
                $subject,
                $emails,
                $data,
                $files
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
                    $this->createDossierHandoverWhenApprovePlan($minute);

                    $subject = 'Biên bản kế hoạch hồ sơ ngoại nghiệp đã được phê duyệt';
                    $type = 1;
                    $contract = $minute->plan->contract;
                    $nguoilap = $minute->plan->user;
                    break;
                case 'handover':
                    $subject = 'Biên bản bàn giao hồ sơ ngoại nghiệp đã được phê duyệt';
                    $type = 4;
                    $contract = $minute->handover->plan->contract;
                    $nguoilap = $minute->handover->user;

                    $minute = $this->createHandoverMinus($minute->handover->toArray(), true, $minute);
                    break;
                case 'usage_register':
                    $this->sendMail(
                        'Yêu cầu đăng ký sử dụng hồ sơ ngoại nghiệp đã được phê duyệt',
                        [
                            'name' => $minute->usageRegister->plan->contract->name,
                            'type' => 7,
                            'lydo' => $request['approval_note'],
                            'nguoidangky' => $minute->usageRegister->registeredBy->name,
                            'ngaydangky' => date('d/m/Y H:i:s', strtotime($minute->usageRegister->created_at)),
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
                    'lydo' => $request['approval_note'],
                    'nguoilap' => $nguoilap,
                ],
                $minute
            );
        }, true);
    }

    private function createDossierHandoverWhenApprovePlan(DossierMinute $minute)
    {
        // check xem kho còn đủ loại giấy tờ ko
        $planDetails = $this->controlDossierTypeQuantity($minute);

        $dossierHandoverService = app(DossierHandoverService::class);

        // xóa data bàn giao cũ của biên bản nếu có
        $dossierHandoverService->deleteByPlanId($minute->plan->id);

        // tạo data bàn giao
        $handover = $dossierHandoverService->store([
            'dossier_plan_id' => $minute->plan->id,
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
                $item['dossier_plan_id'],
                $item['estimated_time'],
                $item['responsible_user_id']
            );

            $item['dossier_handover_id'] = $handover['id'];

            return $item;
        }, $planDetails->toArray());

        $handover->details()->createMany($handoverDetails);
    }

    public function controlDossierTypeQuantity(DossierMinute $minute)
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
                false
            );

            throw new Exception($message);
        } else {
            // update lại số lượng kho
            $planDetails->map(function (&$item) {
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
                'Yêu cầu bổ sung các loại giấy tờ hồ sơ ngoại nghiệp sắp hết',
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
                    $subject = 'Biên bản kế hoạch chứng từ hồ sơ ngoại nghiệp đã bị từ chối';
                    $type = 2;
                    $contract = $minute->plan->contract;
                    $nguoilap = $minute->plan->user;
                    break;
                case 'handover':
                    $subject = 'Biên bản bàn giao hồ sơ ngoại nghiệp đã bị từ chối';
                    $type = 5;
                    $contract = $minute->handover->plan->contract;
                    $nguoilap = $minute->handover->user;
                    break;
                case 'usage_register':
                    $this->sendMail(
                        'Yêu cầu đăng ký sử dụng hồ sơ ngoại nghiệp đã bị từ chối',
                        [
                            'name' => $minute->usageRegister->plan->contract->name,
                            'type' => 8,
                            'lydo' => $request['rejection_note'],
                            'nguoidangky' => $minute->usageRegister->registeredBy->name,
                            'ngaydangky' => date('d/m/Y H:i:s', strtotime($minute->usageRegister->created_at)),
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
