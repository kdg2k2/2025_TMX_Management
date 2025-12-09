@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'ĐXXD Phần mềm', 'url' => route('build-software.index')],
        ['label' => 'Thêm mới', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('build-software.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.build-software.store') }}">
                @method('post')
                @include('admin.pages.build-software.create-edit-form-content')
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
@endsection
