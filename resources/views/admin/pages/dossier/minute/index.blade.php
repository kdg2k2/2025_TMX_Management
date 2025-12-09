@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hồ sơ ngoại nghiệp', 'url' => null],
        ['label' => 'Biên bản', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="accept-modal" title="Xác nhận duyệt biên bản" size="md" method="post" nested="true">
        <x-slot:body>
            <div class="form-group">
                <label>Nhận xét</label>
                <input type="text" class="form-control" name="approval_note">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit variant="success" />
        </x-slot:footer>
    </x-modal>

    <x-modal id="deny-modal" title="Xác nhận từ chối biên bản" size="md" method="post" nested="true">
        <x-slot:body>
            <div class="form-group">
                <label>Nhận xét</label>
                <input type="text" class="form-control" name="rejection_note">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit variant="danger" />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.dossier.minute.list'));
        const acceptUrl = @json(route('api.dossier.minute.accept'));
        const denyUrl = @json(route('api.dossier.minute.deny'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/dossier/minute/script.js?v={{ time() }}"></script>
@endsection
