@extends('admin.pages.dossier.partials.master')
@section('child-content')
    <x-nav-tab id="dossier-usage_register-nav" class="mb-3" style="pills" :tabs="[
        [
            'class' => 'mt-3',
            'title' => 'Dữ liệu',
            'icon' => 'ti ti-info-circle',
            'content' => view('admin.pages.dossier.usage_register.partials.table')->render(),
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
        const urlLoadData = @json(route('api.dossier.usage_register.findByIdContractAndYear'));
        const urlCreateTempExcel = @json(route('api.dossier.usage_register.createTempExcel'));
        const urlUploadExcel = @json(route('api.dossier.usage_register.uploadExcel'));
        const urlSendApproveRequest = @json(route('api.dossier.usage_register.sendApproveRequest'));
    </script>
    <script src="assets/js/dossier/usage_register/script.js"></script>
@endsection
