@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => route('contract.index')],
        ['label' => 'Nhân sự thực hiện', 'url' => null],
    ]" />

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div id="contract-personnel-content">
                        <x-nav-tab id="contract-personnel-tab" style="pills" :tabs="[
                            [
                                'title' => 'Nhân sự thực hiện',
                                'icon' => 'ti ti-users',
                                'content' => view('admin.pages.contract.personnel.partials.general')->render(),
                            ],
                            [
                                'class' => 'border-0',
                                'title' => 'Tổng hợp',
                                'icon' => 'ti ti-report-analytics',
                                'content' => view('admin.pages.contract.personnel.partials.synthetic', [
                                    'personnels' => $personnels,
                                    'years' => $years,
                                    'investors' => $investors,
                                ])->render(),
                            ],
                        ]" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="personnel-modal" size="lg" nested="true">
        <x-slot:body>
            <div class="action d-flex justify-content-between align-items-center mb-2"></div>
            <table class="display w-100"></table>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
        </x-slot:footer>
    </x-modal>
    <x-modal id="import-personnel-modal" method="put" size="sm" nested="true">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Chọn file (.xlsx)
                </label>
                <input type="file" class="form-control" accept=".xlsx" name="file" required>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.contract.list'));
        const contractPersonnelExport = @json(route('contract.personnel.export'));
        const contractPersonnelImport = @json(route('contract.personnel.import'));
        const apiPersonnelList = @json(route('api.personnels.list'));
        const apiContractPersonnelList = @json(route('api.contract.personnel.list'));
        const apiContractPersonnelSynthetic = @json(route('api.contract.personnel.synthetic'));
        const apiContractList = @json(route('api.contract.list'));
        const $personnels = @json($personnels);
        const $isInContract = @json($isInContract);
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/contract/personnel/list.js?v={{ time() }}"></script>
    <script src="assets/js/contract/personnel/modals.js?v={{ time() }}"></script>
    <script src="assets/js/contract/personnel/filter.js?v={{ time() }}"></script>
@endsection
