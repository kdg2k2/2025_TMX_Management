@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Mượn thiết bị', 'url' => route('device.loan.index')],
        ['label' => 'Đăng ký', 'url' => null],
    ]">
        <x-button.list :href="route('device.loan.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.device.loan.store') }}">
                @method('post')
                @include('admin.pages.device.loan.create-edit-form-content')
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
    <script src="assets/js/device/loan/clone-row.js?v={{ time() }}"></script>
@endsection
