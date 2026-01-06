@extends('admin.layout.master')
@section('styles')
    <link rel="stylesheet" href="assets/css/bootstrap-carousel/style.css">
@endsection
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Mượn phương tiện', 'url' => null]]">
        <x-button.create :href="route('vehicle.loan.create')" />
    </x-breadcrumb>

    <div class="mb-2 row">
        <div class="col-lg-2 col-md-4">
            <div class="my-1">
                <label>Phương tiện</label>
                <select name="vehicle_id" id="vehicle-id">
                    <x-select-options :items="$vehicles" :valueFields="['brand', 'license_plate']" />
                </select>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="my-1">
                <label>Trạng thái</label>
                <select name="status" id="status">
                    <x-select-options :items="$status" keyField="original" valueFields="converted" />
                </select>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="my-1">
                <label>Người mượn</label>
                <select name="created_by" id="created-by">
                    <x-select-options :items="$users" />
                </select>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="my-1">
                <label>Trạng thái phương tiện khi trả</label>
                <select name="vehicle_status_return" id="vehicle-status-return">
                    <x-select-options :items="$statusReturn" keyField="original" valueFields="converted" />
                </select>
            </div>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-approve-modal id="approve-modal" title="Xác nhận duyệt đăng ký" size="md" method="post"
        noteName="approval_note"></x-approve-modal>
    <x-approve-modal id="reject-modal" title="Xác nhận từ chối đăng ký" size="md" method="post"
        noteName="rejection_note" buttonVariant="danger"></x-approve-modal>
    <x-modal id="return-modal" title="Xác nhận trả phương tiện" size="md" method="POST" nested="true">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Số km hiện trạng
                </label>
                <input type="number" class="form-control" name="return_km" required>
            </div>
            <div class="my-1">
                <label>Trạng thái phương tiện khi trả</label>
                <select name="vehicle_status_return" required>
                    <x-select-options :items="$statusReturn" keyField="original" valueFields="converted" />
                </select>
            </div>
            <div class="my-1">
                <label>Ghi chú</label>
                <input type="text" class="form-control" name="note">
            </div>
            <hr>
            <div class="my-1">
                <label>
                    Ảnh hiện trạng xe phía trước
                </label>
                <input type="file" class="form-control" name="return_front_image" accept=".png,.jpg,.jpeg,.webp"
                    required>
            </div>
            <div class="my-1">
                <label>
                    Ảnh hiện trạng xe phía sau
                </label>
                <input type="file" class="form-control" name="return_rear_image" accept=".png,.jpg,.jpeg,.webp" required>
            </div>
            <div class="my-1">
                <label>
                    Ảnh hiện trạng xe phía trái
                </label>
                <input type="file" class="form-control" name="return_left_image" accept=".png,.jpg,.jpeg,.webp" required>
            </div>
            <div class="my-1">
                <label>
                    Ảnh hiện trạng xe phía phải
                </label>
                <input type="file" class="form-control" name="return_right_image" accept=".png,.jpg,.jpeg,.webp"
                    required>
            </div>
            <div class="my-1">
                <label>
                    Ảnh hiện trạng công tơ mét
                </label>
                <input type="file" class="form-control" name="return_odometer_image" accept=".png,.jpg,.jpeg,.webp"
                    required>
            </div>
            <hr>
            <div class="my-1">
                <label>Người đổ xăng - nếu có</label>
                <select name="fuel_cost_paid_by">
                    <x-select-options :items="$users" />
                </select>
            </div>
            <div class="my-1">
                <label>
                    Chi phí đổ xăng(vnđ) - nếu có
                </label>
                <input type="number" class="form-control" name="fuel_cost" id="fuel-cost">
            </div>
            <hr>
            <div class="my-1">
                <label>Người bảo dưỡng - nếu có</label>
                <select name="maintenance_cost_paid_by">
                    <x-select-options :items="$users" />
                </select>
            </div>
            <div class="my-1">
                <label>
                    Chi phí bảo dưỡng(vnđ) - nếu có
                </label>
                <input type="number" class="form-control" name="maintenance_cost" id="maintenance-cost">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const listUrl = @json(route('api.vehicle.loan.list'));
        const vehicleLoanApproveUrl = @json(route('vehicle.loan.approve'));
        const vehicleLoanRejectUrl = @json(route('vehicle.loan.reject'));
        const apiVehicleLoanReturn = @json(route('api.vehicle.loan.return'));
    </script>
    <script src="assets/js/bootstrap-carousel/script.js?v={{ time() }}"></script>
    <script src="assets/js/gallery/script.js?v={{ time() }}"></script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/vehicle/loan/list.js?v={{ time() }}"></script>
    <script src="assets/js/vehicle/loan/filter.js?v={{ time() }}"></script>
    <script src="assets/js/components/approve-reject-modal-event.js?v={{ time() }}"></script>
    <script src="assets/js/vehicle/loan/modal.js?v={{ time() }}"></script>
@endsection
