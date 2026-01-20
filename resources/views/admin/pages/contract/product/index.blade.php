@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => null],
        ['label' => 'Sản phẩm', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-2 my-1">
            <label>
                Trạng thái biên bản
            </label>
            <select name="contract_product_minute_status" id="minute-status" class="form-control">
                <x-select-options :items="$minuteStatuses" keyField="original" valueFields="converted" :optionCallback="fn($item) => isset($item['color']) ? 'class=text-' . $item['color'] : ''" />
            </select>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.contract.product.list'));

        const contractProductMinuteApprove = @json(route('contract.product.minute.approve'));
        const contractProductMinuteReject = @json(route('contract.product.minute.reject'));

        const apiContractProductMinuteList = @json(route('api.contract.product.minute.list'));
        const apiContractProductMinuteCreate = @json(route('api.contract.product.minute.create'));
        const apiContractProductMinuteReplace = @json(route('api.contract.product.minute.replace'));
        const apiContractProductMinuteSignatureRequest = @json(route('api.contract.product.minute.signature-request'));
        const apiContractProductMinuteConfirmIssues = @json(route('api.contract.product.minute.confirm-issues'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/contract/product/list.js?v={{ time() }}"></script>
    <script src="assets/js/contract/product/filter.js?v={{ time() }}"></script>
@endsection
