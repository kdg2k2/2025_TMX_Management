@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hồ sơ chuyên môn', 'url' => null],
        ['label' => $pageTitle, 'url' => null],
    ]" />

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
                    <label for="contract-id">
                        Hợp đồng
                        <span class="badge bg-info text-white">Tổng {{ count($contracts) }}</span>
                    </label>
                    <select id="contract-id" class="form-control">
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
                            Người thao tác cuối: <span id="created-name"></span>
                        </li>
                        <li>
                            Đã có dữ liệu: <span id="is-has-data"></span>
                        </li>
                        <li>
                            Đã có biên bản: <span id="is-has-minute"></span>
                        </li>
                        <li>
                            Trạng thái biên bản: <span id="is-minus-pending"></span>
                        </li>
                        @yield('custom-status')
                    </ul>
                    <div>
                        <x-button variant="secondary" :outline="true" size="sm"
                            tooltip="Tải xuống file mẫu Excel dùng để nhập dữ liệu chi tiết, sau đó upload ngược lại lên hệ thống"
                            icon="ti ti-file-spreadsheet" id="download-excel-btn">
                            Tải mẫu
                        </x-button>
                        <x-button variant="success" :outline="true" size="sm"
                            tooltip="Tải lên file Excel mẫu đã được điền dữ liệu" icon="ti ti-file-type-xls"
                            id="upload-modal-btn">
                            Tải lên Excel
                        </x-button>
                        @if ($showCreateMinuteBtn)
                            <x-button variant="warning" :outline="true" size="sm" tooltip="Tạo xem trước biên bản"
                                icon="ti ti-file-type-docx" id="create-minute-modal-btn">
                                Tạo BB
                            </x-button>
                        @endif
                        <x-button variant="info" :outline="true" size="sm" tooltip="Tải file biên bản"
                            icon="ti ti-download" id="download-minute-btn">
                            Tải BB
                        </x-button>
                        <x-button variant="danger" :outline="true" size="sm"
                            tooltip="Gửi mail yêu cầu duyệt biên bản" icon="ti ti-send" id="approve-modal-btn">
                            YC duyệt
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('child-content')
@endsection
@section('modals')
    <x-modal id="upload-modal" title="Tải lên excel dữ liệu đã nhập" size="md" method="post" nested="true">
        <x-slot:body>
            <div class="form-group">
                <label for="file" class="form-label">Chọn file excel</label>
                <input type="file" class="form-control" id="file" name="file" accept=".xlsx" required>
            </div>
            @yield('custom-upload-modal-body')
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="approve-modal" title="Xác nhận yêu cầu duyệt biên bản" size="md" method="post" nested="true">
        <x-slot:body>
            Chắc chắc yêu cầu duyệt biên bản này?
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    @yield('child-modals')
@endsection
@section('scripts')
    <script>
        $showCreateMinuteBtn = @json($showCreateMinuteBtn);
    </script>
    <script src="assets/js/professional-record/base.js"></script>
    @yield('child-scripts')
@endsection
