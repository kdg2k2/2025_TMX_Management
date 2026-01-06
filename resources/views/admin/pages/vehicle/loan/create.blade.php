@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Mượn phương tiện', 'url' => route('vehicle.loan.index')],
        ['label' => 'Đăng ký', 'url' => null],
    ]">
        <x-button.list :href="route('vehicle.loan.index')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.vehicle.loan.store') }}">
                @method('post')
                @include('admin.pages.vehicle.loan.create-form-content')
                @if (count($vehicles) > 0)
                    <div class="my-1 col-12 text-center">
                        <x-button-submit />
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $data = @json($data ?? null);
    </script>
    <script src="assets/js/http-request/base-store-and-update.js?v={{ time() }}"></script>
    <script src="assets/js/vehicle/loan/base-store-and-update.js?v={{ time() }}"></script>
@endsection
