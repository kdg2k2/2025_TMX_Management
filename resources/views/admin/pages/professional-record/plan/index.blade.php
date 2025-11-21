@extends('admin.pages.professional-record.partials.master')
@section('child-content')
    <x-nav-tab id="professional-record-plan-nav" class="mb-3" style="pills" :tabs="[
        [
            'class' => 'mt-3',
            'title' => 'Dữ liệu',
            'icon' => 'ti ti-info-circle',
            'content' => view('admin.pages.professional-record.plan.partials.table')->render(),
        ],
        [
            'class' => 'mt-3',
            'title' => 'Xem biên bản',
            'icon' => 'ti ti-files',
            'content' => view('admin.pages.professional-record.partials.preview')->render(),
        ],
    ]" />
@endsection
@section('custom-upload-modal-body')
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
@endsection
@section('child-scripts')
    <script>
        const $contracts = @json($contracts);
        const urlLoadData = @json(route('api.professional-record.plan.findByIdContractAndYear'));
        const urlCreateTempExcel = @json(route('api.professional-record.plan.createTempExcel'));
        const urlUploadExcel = @json(route('api.professional-record.plan.uploadExcel'));
        const urlSendApproveRequest = @json(route('api.professional-record.plan.sendApproveRequest'));
    </script>
    <script src="assets/js/professional-record/plan/script.js"></script>
@endsection
