@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Vé tàu xe', 'url' => route('plane-ticket.index')],
        ['label' => 'Thêm mới', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('plane-ticket.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.plane-ticket.store') }}">
                @method('post')
                @include('admin.pages.plane-ticket.create-edit-form-content')
                <div class="my-1 col-12 text-center">
                    <x-button-submit />
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="assets/js/clone-row/script.js?v={{ time() }}"></script>
    <script src="assets/js/set-hide-and-required/script.js?v={{ time() }}"></script>
    <script src="assets/js/plane-ticket/base-create-edit-form.js?v={{ time() }}"></script>
@endsection
