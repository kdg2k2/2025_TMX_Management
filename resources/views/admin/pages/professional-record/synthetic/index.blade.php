@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hồ sơ chuyên môn', 'url' => null],
        ['label' => 'Tổng hợp chứng từ', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <div class="row">
                <div class="col-2">
                    <label for="year">Năm</label>
                    <select name="year" id="year" class="form-control">
                        <x-select-options :items="$years" />
                    </select>
                </div>
                <div class="col-7">
                    <label for="contract-id">
                        Hợp đồng
                        <span class="badge bg-info text-white">Tổng {{ count($contracts) }}</span>
                    </label>
                    <select id="contract-id" class="form-control">
                        <x-select-options :items="$contracts" />
                    </select>
                </div>
                <div class="col-md-1 d-grid align-content-center">
                    <x-button id="download-excel-btn" :outline="true" icon="ti ti-download" variant="success" tooltip="Tải file thống kê"
                        size="md">Tải</x-button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <div class="card custom-card">
            <div class="card-body">
                <iframe src="" frameborder="0" class="w-100" style="height:82vh;" scrolling="auto"></iframe>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const $contracts = @json($contracts);
        const createSyntheticFile = @json(route('api.professional-record.synthetic.create-synthetic-file'));
    </script>
    <script src="assets/js/professional-record/synthetic/script.js"></script>
@endsection
