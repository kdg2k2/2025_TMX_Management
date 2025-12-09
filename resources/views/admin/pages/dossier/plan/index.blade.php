@extends('admin.pages.dossier.partials.master')
@section('child-content')
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
@section('child-modals')
    <x-modal id="create-minute-modal" title="Tạo biên bản kế hoạch" size="md" method="post" nested="true">
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
@endsection
@section('child-scripts')
    <script>
        const $contracts = @json($contracts);
        const urlLoadData = @json(route('api.dossier.plan.findByIdContractAndYear'));
        const urlCreateTempExcel = @json(route('api.dossier.plan.createTempExcel'));
        const urlUploadExcel = @json(route('api.dossier.plan.uploadExcel'));
        const urlCreateMinute = @json(route('api.dossier.plan.createMinute'));
        const urlSendApproveRequest = @json(route('api.dossier.plan.sendApproveRequest'));
    </script>
    <script src="assets/js/dossier/plan/script.js?v={{ time() }}"></script>
@endsection
