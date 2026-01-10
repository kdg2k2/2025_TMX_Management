@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Đề nghị/Phát hành', 'url' => route('official-document.index')],
        ['label' => 'Tái sử dụng', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('official-document.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.official-document.update', ['id' => $data['id']]) }}">
                @method('patch')
                @include('admin.pages.official-document.create-edit-form-content')
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
    <script src="assets/js/set-hide-and-required/script.js?v={{ time() }}"></script>
    <script src="assets/js/http-request/base-store-and-update.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/base-store-and-update.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/update.js?v={{ time() }}"></script>
@endsection
