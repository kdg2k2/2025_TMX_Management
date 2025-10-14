@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => route('contract.index')],
        ['label' => 'Cập nhật', 'url' => null],
    ]">
        <button class="btn btn-sm btn-success" onclick="window.location='{{ route('contract.index') }}'" type="button"
            data-bs-placement="top" data-bs-original-title="Danh sách">
            <i class="ti ti-list"></i>
        </button>
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
    <script src="assets/js/http-request/base-store-and-update.js"></script>
    <script src="assets/js/contract/base-store-and-update.js"></script>
    <script src="assets/js/contract/update.js"></script>
@endsection
