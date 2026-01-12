@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Đăng ký/Phê duyệt mã kaspersky', 'url' => route('kaspersky.registration.index')],
        ['label' => 'Thêm mới', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('kaspersky.registration.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.kaspersky.registration.store') }}">
                @method('post')
                @include('admin.pages.kaspersky.registration.create-form-content')
                <div class="my-1 col-12 text-center">
                    <x-button-submit />
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $data = null;
    </script>
    <script src="assets/js/clone-row/script.js?v={{ time() }}"></script>
    <script src="assets/js/set-hide-and-required/script.js?v={{ time() }}"></script>
    <script src="assets/js/kaspersky/registration/base-create-form.js?v={{ time() }}"></script>
    <script src="assets/js/http-request/base-store-and-update.js?v={{ time() }}"></script>
@endsection
