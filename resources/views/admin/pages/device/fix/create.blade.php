@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Sửa chữa thiết bị', 'url' => route('device.fix.index')],
        ['label' => 'Đăng ký', 'url' => null],
    ]">
        <x-button.list :href="route('device.fix.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.device.fix.store') }}">
                @method('post')
                @include('admin.pages.device.fix.create-form-content')
                @if (count($devices) > 0)
                    <div class="my-1 col-12 text-center">
                        <x-button-submit />
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="assets/js/clone-row/script.js?v={{ time() }}"></script>
    <script src="assets/js/device/fix/clone-row.js?v={{ time() }}"></script>
@endsection
