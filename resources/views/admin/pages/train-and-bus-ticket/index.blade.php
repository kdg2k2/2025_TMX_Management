@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Vé tàu xe', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('train-and-bus-ticket.create')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-approve-modal id="approve-modal" title="Xác nhận duyệt biên bản" size="md" method="post"
        noteName="approval_note"></x-approve-modal>
    <x-approve-modal id="reject-modal" title="Xác nhận từ chối biên bản" size="md" method="post"
        noteName="rejection_note" buttonVariant="danger"></x-approve-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.train-and-bus-ticket.list'));
        const trainAndBusTicketApproveUrl = @json(route('train-and-bus-ticket.approve'));
        const trainAndBusTicketRejectUrl = @json(route('train-and-bus-ticket.reject'));
        const trainAndBusTicketDetailIndex = @json(route('train-and-bus-ticket.detail.index'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/train-and-bus-ticket/modals.js"></script>
    <script src="assets/js/train-and-bus-ticket/list.js"></script>
@endsection
