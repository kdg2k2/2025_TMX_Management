@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Nghỉ phép', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('leave-request.create')" />
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-2 col-md-4 mb-1">
            <label>
                Người đăng ký
            </label>
            <select id="created-by">
                <x-select-options :items="$users" />
            </select>
        </div>
        <div class="col-lg-2 col-md-4 mb-1">
            <label>
                Trạng thái duyệt đăng ký
            </label>
            <select id="approval-status">
                <x-select-options :items="$approvalStatus" keyField="original" valueFields="converted" />
            </select>
        </div>
        <div class="col-lg-2 col-md-4 mb-1">
            <label>
                Trạng thái duyệt điều chỉnh
            </label>
            <select id="adjust-approval-status">
                <x-select-options :items="$adjustApprovalStatus" keyField="original" valueFields="converted" />
            </select>
        </div>
        <div class="col-lg-2 col-md-4 mb-1">
            <label>
                Từ ngày
            </label>
            <input type="date" class="form-control" id="from-date-filter">
        </div>
        <div class="col-lg-2 col-md-4 mb-1">
            <label>
                Đến ngày
            </label>
            <input type="date" class="form-control" id="to-date-filter">
        </div>
    </div>
    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="modal-approve-request" title="" size="md" method="POST" nested="true">
        <x-slot:body>
            <input type="hidden" name="approval_status">
            <div>
                <label>
                    Ghi chú
                </label>
                <input class="form-control" type="text" name="approval_note" required>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit variant="primary" />
        </x-slot:footer>
    </x-modal>

    <x-modal id="modal-adjust-request" title="Yêu cầu cập nhật nghỉ phép" size="md" method="POST" nested="true">
        <x-slot:body>
            @include('admin.pages.leave-request.create-adjust-form-content', ['colClass' => ''])
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit variant="primary" />
        </x-slot:footer>
    </x-modal>

    <x-modal id="modal-adjust-approve-request" title="" size="md" method="POST" nested="true">
        <x-slot:body>
            <input type="hidden" name="adjust_approval_status">
            <div>
                <label>
                    Ghi chú
                </label>
                <input class="form-control" type="text" name="adjust_approval_note" required>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit variant="primary" />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.leave-request.list'));
        const apiLeaveRequestApprove = @json(route('api.leave-request.approve'));
        const apiLeaveRequestReject = @json(route('api.leave-request.reject'));
        const apiLeaveRequestAdjust = @json(route('api.leave-request.adjust'));
        const apiLeaveRequestAdjustApprove = @json(route('api.leave-request.adjust-approve'));
        const apiLeaveRequestAdjustReject = @json(route('api.leave-request.adjust-reject'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/leave-request/list.js?v={{ time() }}"></script>
    <script src="assets/js/leave-request/modals.js?v={{ time() }}"></script>
    <script src="assets/js/leave-request/get-total-leave-days.js?v={{ time() }}"></script>
@endsection
