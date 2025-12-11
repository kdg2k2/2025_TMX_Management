@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Vé máy bay', 'url' => null],
        ['label' => 'Chi tiết', 'url' => null],
        ['label' => 'Cập nhật', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('plane-ticket.detail.index', ['id' => $data['plane_ticket_id']])" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row"
                action="{{ route('api.plane-ticket.detail.update', ['id' => $data['id']]) }}">
                @method('patch')
                @include('admin.pages.plane-ticket.detail.edit-form-content')
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
    <script src="assets/js/plane-ticket/detail/update.js?v={{ time() }}"></script>
@endsection
