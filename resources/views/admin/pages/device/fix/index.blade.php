@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Sửa chữa thiết bị', 'url' => null]]">
        <x-button.create :href="route('device.fix.create')" />
    </x-breadcrumb>

    <div class="mb-2 row">
        <div class="col-lg-2 col-md-4">
            <div class="my-1">
                <label>Thiết bị</label>
                <select name="device_id" id="device-id">
                    <x-select-options :items="$devices" />
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
                <label>Người đăng ký</label>
                <select name="created_by" id="created-by">
                    <x-select-options :items="$users" />
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
    <x-modal id="fixed-modal" title="Xác nhận đã sửa thiết bị" size="md" method="POST" nested="true">
        <x-slot:body>
            Chắc chắn xác nhận đã sửa thiết bị này?
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const listUrl = @json(route('api.device.fix.list'));
        const deviceFixApproveUrl = @json(route('device.fix.approve'));
        const deviceFixRejectUrl = @json(route('device.fix.reject'));
        const apiDeviceFixFixed = @json(route('api.device.fix.fixed'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/device/fix/list.js?v={{ time() }}"></script>
    <script src="assets/js/device/fix/filter.js?v={{ time() }}"></script>
    <script src="assets/js/components/approve-reject-modal-event.js?v={{ time() }}"></script>
    <script src="assets/js/device/fix/modal.js?v={{ time() }}"></script>
@endsection
