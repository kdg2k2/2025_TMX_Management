@extends('admin.pages.professional-record.partials.master')
@section('custom-status')
    <li>
        Số lần đã bàn giao: <span id="handover-in-count"></span>
    </li>
@endsection
@section('child-content')
    <x-nav-tab id="professional-record-handover-nav" class="mb-3" style="pills" :tabs="[
        [
            'class' => 'mt-3',
            'title' => 'Dữ liệu',
            'icon' => 'ti ti-info-circle',
            'content' => view('admin.pages.professional-record.handover.partials.table')->render(),
        ],
        [
            'class' => 'mt-3',
            'title' => 'Xem biên bản',
            'icon' => 'ti ti-files',
            'content' => view('admin.pages.professional-record.partials.preview')->render(),
        ],
    ]" />
@endsection
@section('child-scripts')
    <script>
        const $contracts = @json($contracts);
        const urlLoadData = @json(route('api.professional-record.handover.findByIdContractAndYear'));
        const urlCreateTempExcel = @json(route('api.professional-record.handover.createTempExcel'));
        const urlUploadExcel = @json(route('api.professional-record.handover.uploadExcel'));
        const urlCreateMinute = @json(route('api.professional-record.handover.createMinute'));
        const urlSendApproveRequest = @json(route('api.professional-record.handover.sendApproveRequest'));
    </script>
    <script src="assets/js/professional-record/handover/script.js"></script>
@endsection
