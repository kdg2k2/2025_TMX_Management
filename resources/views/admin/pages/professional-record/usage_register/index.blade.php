@extends('admin.pages.professional-record.partials.master')
@section('child-content')
    <x-nav-tab id="professional-record-usage_register-nav" class="mb-3" style="pills" :tabs="[
        [
            'class' => 'mt-3',
            'title' => 'Dữ liệu',
            'icon' => 'ti ti-info-circle',
            'content' => view('admin.pages.professional-record.usage_register.partials.table')->render(),
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
        const urlLoadData = @json(route('api.professional-record.usage_register.findByIdContractAndYear'));
        const urlCreateTempExcel = @json(route('api.professional-record.usage_register.createTempExcel'));
        const urlUploadExcel = @json(route('api.professional-record.usage_register.uploadExcel'));
        const urlSendApproveRequest = @json(route('api.professional-record.usage_register.sendApproveRequest'));
    </script>
    <script src="assets/js/professional-record/usage_register/script.js"></script>
@endsection
