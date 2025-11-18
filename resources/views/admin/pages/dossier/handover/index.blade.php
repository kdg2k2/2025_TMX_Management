@extends('admin.pages.dossier.partials.master')
@section('custom-status')
    <li>
        Số lần đã bàn giao: <span id="handover-in-count"></span>
    </li>
@endsection
@section('child-content')
    <x-nav-tab id="dossier-handover-nav" class="mb-3" style="pills" :tabs="[
        [
            'class' => 'mt-3',
            'title' => 'Dữ liệu',
            'icon' => 'ti ti-info-circle',
            'content' => view('admin.pages.dossier.handover.partials.table')->render(),
        ],
        [
            'class' => 'mt-3',
            'title' => 'Xem biên bản',
            'icon' => 'ti ti-files',
            'content' => view('admin.pages.dossier.partials.preview')->render(),
        ],
    ]" />
@endsection
@section('child-scripts')
    <script>
        const $contracts = @json($contracts);
        const urlLoadData = @json(route('api.dossier.handover.findByIdContractAndYear'));
        const urlCreateTempExcel = @json(route('api.dossier.handover.createTempExcel'));
        const urlUploadExcel = @json(route('api.dossier.handover.uploadExcel'));
        const urlCreateMinute = @json(route('api.dossier.handover.createMinute'));
        const urlSendApproveRequest = @json(route('api.dossier.handover.sendApproveRequest'));
    </script>
    <script src="assets/js/dossier/handover/script.js"></script>
@endsection
