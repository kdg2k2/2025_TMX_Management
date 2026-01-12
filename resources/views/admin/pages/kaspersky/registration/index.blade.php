@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Đăng ký/Phê duyệt mã kaspersky', 'url' => null],
    ]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('kaspersky.registration.create')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-approve-modal id="approve-modal" title="Xác nhận duyệt đăng ký" size="md" method="post" noteName="approval_note">
        <x-slot:extraFields>
            <div class="my-1">
                <label>
                    Mã kaspersky
                </label>
                <select name="codes[]" class="form-control" required multiple>
                    <x-select-options :items="$codes" :emptyOption="false" :valueFields="['created_at', 'available_quantity_message']" />
                </select>
            </div>
        </x-slot:extraFields>
    </x-approve-modal>
    <x-approve-modal id="reject-modal" title="Xác nhận từ chối đăng ký" size="md" method="post"
        noteName="rejection_note" buttonVariant="danger"></x-approve-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.kaspersky.registration.list'));
        const kasperskyRegistrationApproveUrl = @json(route('kaspersky.registration.approve'));
        const kasperskyRegistrationRejectUrl = @json(route('kaspersky.registration.reject'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/components/approve-reject-modal-event.js?v={{ time() }}"></script>
    <script src="assets/js/kaspersky/registration/list.js?v={{ time() }}"></script>
@endsection
