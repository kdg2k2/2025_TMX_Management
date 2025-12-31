<?php

namespace App\Services;

use App\Repositories\VehicleLoanRepository;
use Exception;

class VehicleLoanService extends BaseService
{
    public function __construct(
        private VehicleService $vehicleService,
        private UserService $userService,
        private HandlerUploadFileService $handlerUploadFileService,
    ) {
        $this->repository = app(VehicleLoanRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['vehicle_status_return']))
            $array['vehicle_status_return'] = $this->repository->getStatusReturn($array['vehicle_status_return']);
        if (isset($array['estimated_vehicle_return_date']))
            $array['estimated_vehicle_return_date'] = $this->formatDateForPreview($array['estimated_vehicle_return_date']);

        foreach ([
            'vehicle_pickup_time',
            'approved_at',
            'returned_at',
        ] as $item)
            if (isset($array[$item]))
                $array[$item] = $this->formatDateTimeForPreview($array[$item]);

        foreach ([
            'before_front_image',
            'before_rear_image',
            'before_left_image',
            'before_right_image',
            'before_odometer_image',
            'return_front_image',
            'return_rear_image',
            'return_left_image',
            'return_right_image',
            'return_odometer_image',
        ] as $item)
            if (isset($array[$item]))
                $array[$item] = $this->getAssetUrl($array[$item]);

        return $array;
    }

    public function getBaseDataForLCView($listView = true)
    {
        return [
            'status' => $this->repository->getStatus(),
            'statusReturn' => $this->repository->getStatusReturn(),
            'users' => $this->userService->list([
                'load_relations' => false,
                'columns' => ['id', 'name'],
            ]),
            'vehicles' => $this->vehicleService->list(!$listView ? [
                'status' => 'normal'
            ] : []),
        ];
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            foreach ($request['details'] as $item) {
                $data = $this->repository->store([
                    'created_by' => $request['created_by'],
                    'vehicle_id' => $item['vehicle_id'],
                    'vehicle_pickup_time' => $request['vehicle_pickup_time'],
                    'estimated_vehicle_return_date' => $item['estimated_vehicle_return_date'],
                    'destination' => $item['destination'],
                    'note' => $item['note'],
                    ...collect([
                        'before_front_image',
                        'before_rear_image',
                        'before_left_image',
                        'before_right_image',
                        'before_odometer_image',
                    ])->mapWithKeys(fn($i) => [
                        $i => $this->handlerUploadFileService->storeAndRemoveOld(
                            $item[$i],
                            $this->repository->model->getTable(),
                            'before'
                        )
                    ])->all()
                ]);

                $data->vehicle()->update([
                    'status' => 'loaned',
                    'user_id' => $request['created_by'],
                    'destination' => $item['destination'],
                ]);

                $this->sendMail($data['id'], 'Yêu cầu mượn');
            }
        }, true);
    }

    public function approve(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Phê duyệt mượn');
        }, true);
    }

    public function reject(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $data->vehicle()->update([
                'status' => 'normal',
                'user_id' => null,
                'destination' => null,
            ]);
            $this->sendMail($data['id'], 'Từ chối mượn');
        }, true);
    }

    public function return(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findById($request['id']);
            if (!in_array($this->getUserId(), [1, $data['created_by']]))
                throw new Exception('Chỉ người mượn mới có thể trả xe!');

            foreach ([
                'return_front_image',
                'return_rear_image',
                'return_left_image',
                'return_right_image',
                'return_odometer_image',
            ] as $item) {
                $request[$item] = $this->handlerUploadFileService->storeAndRemoveOld(
                    $request[$item],
                    $this->repository->model->getTable(),
                    'return'
                );
            }
            $data->update($request);

            $data->vehicle()->update([
                'status' => $request['vehicle_status_return'],
                'user_id' => null,
                'destination' => null,
            ]);
            $this->sendMail($data['id'], 'Trả');
        }, true);
    }

    private function sendMail(int $id, string $subject)
    {
        $data = $this->findById($id, true, true);
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob(
            'emails.vehicle-loan',
            $subject . ' xe',
            $emails,
            [
                'data' => $data,
            ]
        ));
    }

    private function getEmails($data)
    {
        return $this->userService->getEmails([
            collect([$data['created_by']['id'], $data['approved_by']['id'] ?? null])->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->getUserIdByScheduleKey('VEHICLE_LOAN')
        ]);
    }
}
