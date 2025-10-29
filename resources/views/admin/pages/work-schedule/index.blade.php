@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Lịch công tác', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('work-schedule.create')" />
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-3 col-md-4 mb-1">
            <label>
                Người đăng ký
            </label>
            <select id="created-by">
                <x-select-options :items="$users" />
            </select>
        </div>
        <div class="col-lg-3 col-md-4 mb-1">
            <label>
                Kiểu chương trình
            </label>
            <select id="type-program">
                <x-select-options :items="$typeProgram" keyField="original" valueFields="converted" />
            </select>
        </div>
        <div class="col-lg-3 col-md-4 mb-1">
            <label>
                Hợp đồng
            </label>
            <select id="contracts-id">
                <x-select-options :items="$contracts" />
            </select>
        </div>
        <div class="col-lg-3 col-md-4 mb-1">
            <label>
                Trạng thái duyệt đăng ký
            </label>
            <select id="approval-status">
                <x-select-options :items="$approvalStatus" keyField="original" valueFields="converted" />
            </select>
        </div>
        <div class="col-lg-3 col-md-4 mb-1">
            <label>
                Trạng thái duyệt kết thúc
            </label>
            <select id="return-approval-status">
                <x-select-options :items="$returnApprovalStatus" keyField="original" valueFields="converted" />
            </select>
        </div>
        <div class="col-lg-3 col-md-4 mb-1">
            <label>
                Trạng thái kết thúc công tác
            </label>
            <select id="is-completed">
                <x-select-options :items="$isCompleted" keyField="original" valueFields="converted" selected="" />
            </select>
        </div>
        <div class="col-lg-3 col-md-4 mb-1">
            <label>
                Từ ngày
            </label>
            <input type="date" class="form-control" id="from-date">
        </div>
        <div class="col-lg-3 col-md-4 mb-1">
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

    <x-modal id="modal-return-request" title="Yêu cầu kết thúc công tác" size="md" method="POST" nested="true">
        <x-slot:body>
            <div>
                <label>
                    Thời gian về
                </label>
                <input class="form-control" type="datetime-local" name="return_datetime" required>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit variant="primary" />
        </x-slot:footer>
    </x-modal>

    <x-modal id="modal-return-approve-request" title="" size="md" method="POST" nested="true">
        <x-slot:body>
            <input type="hidden" name="return_approval_status">
            <div>
                <label>
                    Ghi chú
                </label>
                <input class="form-control" type="text" name="return_approval_note" required>
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
        const listUrl = @json(route('api.work-schedule.list'));
        const apiWorkScheduleApprove = @json(route('api.work-schedule.approve'));
        const apiWorkScheduleReject = @json(route('api.work-schedule.reject'));
        const apiWorkScheduleReturn = @json(route('api.work-schedule.return'));
        const apiWorkScheduleReturnApprove = @json(route('api.work-schedule.return-approve'));
        const apiWorkScheduleReturnReject = @json(route('api.work-schedule.return-reject'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/work-schedule/list.js"></script>
    <script src="assets/js/work-schedule/modals.js"></script>
@endsection
