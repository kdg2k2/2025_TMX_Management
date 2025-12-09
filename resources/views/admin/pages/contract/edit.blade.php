@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => route('contract.index')],
        ['label' => 'Cập nhật', 'url' => null],
    ]">
        <x-button size="sm" icon="ti ti-list" tooltip="Danh sách" href="{{ route('contract.index') }}"></x-button>
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form"
                action="{{ route('api.contract.update', [
                    'id' => $data['id'],
                ]) }}">
                @method('patch')
                @include('admin.pages.contract.create-edit-form-content')
                <div class="my-1 col-12 text-center">
                    <x-button-submit />
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const $data = @json($data ?? null);
    </script>
    <script src="assets/js/http-request/base-store-and-update.js?v={{ time() }}"></script>
    <script src="assets/js/contract/vat-calculator.js?v={{ time() }}"></script>
    <script src="assets/js/contract/base-store-and-update.js?v={{ time() }}"></script>
    <script src="assets/js/contract/update.js?v={{ time() }}"></script>
@endsection
