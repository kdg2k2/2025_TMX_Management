@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Vé tàu xe', 'url' => null],
        ['label' => 'Chi tiết', 'url' => null],
        ['label' => 'Cập nhật', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('train-and-bus-ticket.detail.index', ['id' => $data['train_and_bus_ticket_id']])" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <form id="submit-form" class="row" action="{{ route('api.train-and-bus-ticket.detail.update', ['id' => $data['id']]) }}">
                @method('patch')
                @include('admin.pages.train-and-bus-ticket.detail.edit-form-content')
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
    <script src="assets/js/http-request/base-store-and-update.js"></script>
    <script src="assets/js/train-and-bus-ticket/detail/update.js"></script>
@endsection
