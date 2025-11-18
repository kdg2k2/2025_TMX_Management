@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hồ sơ ngoại nghiệp', 'url' => null],
        ['label' => 'Lập kế hoạch', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body row">
            <div class="col-md-8 d-grid">
                <div>
                    <label for="year">Năm</label>
                    <select name="year" id="year" class="form-control">
                        <x-select-options :items="$years" />
                    </select>
                </div>
                <div class="mt-3">
                    <label for="contract_id">
                        Hợp đồng
                        <span class="badge bg-info text-white">Tổng {{ count($contracts) }}</span>
                    </label>
                    <select name="contract_id" id="contract_id" class="form-control">
                        <x-select-options :items="$contracts" />
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <h5 class="m-0">
                        Thông tin chung:
                    </h5>
                    <ul class="mt-3">
                        <li>
                            Người thao tác cuối: <span id="createdName"></span>
                        </li>
                        <li>
                            Đã có dữ liệu: <span id="isHasData"></span>
                        </li>
                        <li>
                            Đã có biên bản: <span id="isHasMinute"></span>
                        </li>
                        <li>
                            Trạng thái biên bản: <span id="isMinusPending"></span>
                        </li>
                    </ul>
                    <div>
                        <x-button variant="secondary" :outline="true" size="sm"
                            tooltip="Tải xuống file mẫu Excel dùng để nhập dữ liệu chi tiết, sau đó upload ngược lại lên hệ thống"
                            icon="ti ti-file-spreadsheet" id="downloadExcelBtn">
                            Tải mẫu
                        </x-button>
                        <x-button variant="success" :outline="true" size="sm"
                            tooltip="Tải lên file Excel mẫu đã được điền dữ liệu" icon="ti ti-file-type-xls"
                            id="uploadModalBtn">
                            Tải lên dữ liệu
                        </x-button>
                        <x-button variant="warning" :outline="true" size="sm" tooltip="Tạo xem trước biên bản"
                            icon="ti ti-file-type-docx" id="createMinuteModalBtn">
                            Tạo BB
                        </x-button>
                        <x-button variant="info" :outline="true" size="sm" tooltip="Tải file biên bản"
                            icon="ti ti-download" id="downloadMinuteBtn">
                            Tải BB
                        </x-button>
                        <x-button variant="danger" :outline="true" size="sm"
                            tooltip="Gửi mail yêu cầu duyệt biên bản" icon="ti ti-send" id="approveModalBtn">
                            YC duyệt
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-nav-tab id="dossier-plan-nav" class="mb-3" style="pills" :tabs="[
        [
            'class' => 'mt-3',
            'title' => 'Dữ liệu',
            'icon' => 'ti ti-info-circle',
            'content' => view('admin.pages.dossier.plan.partials.table')->render(),
        ],
        [
            'class' => 'mt-3',
            'title' => 'Xem biên bản',
            'icon' => 'ti ti-files',
            'content' => view('admin.pages.dossier.partials.preview')->render(),
        ],
    ]" />
@endsection
@section('modals')
    <x-modal id="uploadModal" title="Tải lên excel dữ liệu đã nhập" size="md" method="post" nested="true">
        <x-slot:body>
            <div class="form-group">
                <label for="file" class="form-label">Chọn file excel</label>
                <input type="file" class="form-control" id="file" name="file" accept=".xlsx">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="createMinuteModal" title="Tạo biên bản kế hoạch" size="md" method="post" nested="true">
        <x-slot:body>
            <div class="form-group">
                <label for="handover-date" class="form-label">Ngày bàn giao</label>
                <input type="date" class="form-control" id="handover-date" name="handover_date" required>
            </div>
            <div class="form-group">
                <label for="received-by" class="form-label">Người nhận</label>
                <select class="form-control" id="received-by" name="received_by" required>
                    <option value="">Chọn</option>
                    @foreach ($users as $item)
                        <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="approveModal" title="Xác nhận yêu cầu duyệt biên bản" size="md" method="post" nested="true">
        <x-slot:body>
            Chắc chắc yêu cầu duyệt biên bản này?
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng"
                data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const $contracts = @json($contracts);
        const urlLoadData = @json(route('api.dossier.plan.findByIdContractAndYear'));
        const urlCreateTempExcel = @json(route('api.dossier.plan.createTempExcel'));
        const urlUploadExcel = @json(route('api.dossier.plan.uploadExcel'));
        const urlCreateMinute = @json(route('api.dossier.plan.createMinute'));
        const urlSendApproveRequest = @json(route('api.dossier.plan.sendApproveRequest'));
    </script>
    <script src="assets/js/dossier/base.js"></script>
    <script src="assets/js/dossier/plan/script.js"></script>
@endsection
