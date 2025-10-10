@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => null],
        ['label' => 'Nhà đầu tư', 'url' => null],
    ]">
        <button class="btn btn-sm btn-success" onclick="window.location='{{ route('contract.investor.create') }}'"
            type="button" data-bs-placement="top" data-bs-original-title="Thêm mới">
            <i class="ti ti-plus"></i>
        </button>
    </x-breadcrumb>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <table class="display w-100" id="datatable"></table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.contract.investor.list'));
        const editUrl = @json(route('contract.investor.edit'));
        const deleteUrl = @json(route('contract.investor.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/contract/investor/list.js"></script>
@endsection
