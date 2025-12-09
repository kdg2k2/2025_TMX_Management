@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Nhân sự', 'url' => route('personnels.index')],
        ['label' => 'Cột thông tin bổ sung', 'url' => route('personnels.custom-field.index')],
        ['label' => 'Cập nhật', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('personnels.custom-field.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row"
                action="{{ route('api.personnels.custom-field.update', [
                    'id' => $data['id'],
                ]) }}">
                @method('patch')
                @include('admin.pages.personnels.custom-field.create-edit-form-content')
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
    <script src="assets/js/personnels/custom-field/udpate.js?v={{ time() }}"></script>
    <script src="assets/js/http-request/base-store-and-update.js?v={{ time() }}"></script>
    <script src="assets/js/personnels/custom-field/base-store-and-update.js?v={{ time() }}"></script>
@endsection
