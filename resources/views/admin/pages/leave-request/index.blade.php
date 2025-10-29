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
            <input type="date" class="form-control" id="from-date">
        </div>
        <div class="col-lg-2 col-md-4 mb-1">
            <label>
                Đến ngày
            </label>
            <input type="date" class="form-control" id="to-date">
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

    <x-modal id="modal-adjust-request" title="Yêu cầu kết thúc công tác" size="md" method="POST" nested="true">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Thời gian bắt đầu
                </label>
                <input class="form-control" type="date" name="from_date" required>
            </div>
            <div class="my-1">
                <label>
                    Thời gian kết thúc
                </label>
                <input class="form-control" type="date" name="to_date" required>
            </div>
            <div class="my-1">
                <div class="form-group">
                    <label>
                        Kiểu đăng ký
                    </label>
                    <select name="type" required>
                        <x-select-options :items="$types" keyField="original" valueFields="converted" :emptyOption="false" />
                    </select>
                </div>
            </div>
            <div class="my-1">
                <label>
                    Tông số ngày nghỉ
                </label>
                <input class="form-control bg-light" type="text" name="total_leave_days" readonly required>
            </div>
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
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/leave-request/list.js"></script>
    <script src="assets/js/leave-request/modals.js"></script>
@endsection
