@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Thông tin cá nhân', 'url' => null]]">
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title">
                Cập nhật thông tin cá nhân
            </div>
        </div>
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.profile.update') }}">
                @method('patch')
                @include('admin.pages.profile.form')
                <div class="my-1 col-12 text-center">
                    <x-button-submit />
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const $data = @json($user ?? null);
    </script>
    <script src="assets/js/http-request/base-store-and-update.js?v={{ time() }}"></script>
@endsection
