@extends('admin.layout.master')
@section('styles')
    <link rel="stylesheet" href="assets/css/gallery/style.css">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endsection
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Thiết bị', 'url' => null],
        ['label' => 'Thư viện ảnh', 'url' => null],
    ]">
        <x-button.list :href="route('device.index')" />
        <x-button.create class="ms-1" onclick="openStoreModal(this)" />
    </x-breadcrumb>

    <div class="container-fluid">
        <div id="cards-container" class="row gy-4"></div>

        <nav aria-label="Page navigation" class="py-3">
            <ul id="pagination" class="pagination justify-content-center mb-0 flex-wrap"></ul>
        </nav>
    </div>
@endsection
@section('modals')
    <x-modal id="modal-store" title="Thêm ảnh" size="lg" method="store" nested="true">
        <x-slot:body>
            <div class="dropzone" id="store-dropzone">
                <div class="dropzone-wrapper">
                    <div class="dz-message needsclick"><i class="fa-solid fa-cloud-arrow-up"></i>
                        <h6 class="f-w-600">Thả file hoặc click để chọn file upload</h6>
                        <span class="note needsclick">Chỉ nhận ảnh <strong>(.png,.jpg,.jpeg,.webp)</strong></span>
                    </div>
                </div>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button.submit variant="success" />
        </x-slot:footer>
    </x-modal>

    <x-modal id="modal-update" title="Cập nhật ảnh" size="md" method="patch" nested="true">
        <x-slot:body>
            <div class="dropzone" id="update-dropzone">
                <div class="dropzone-wrapper">
                    <div class="dz-message needsclick"><i class="fa-solid fa-cloud-arrow-up"></i>
                        <h6 class="f-w-600">Thả file hoặc click để chọn file upload</h6>
                        <span class="note needsclick">Chỉ nhận 1 ảnh <strong>(.png,.jpg,.jpeg,.webp)</strong></span>
                    </div>
                </div>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button.submit variant="warning" />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js?v={{ time() }}"></script>
    <script>
        const deviceId = new URL(window.location.href).searchParams.get(
            "device_id"
        );
        const listUrl = @json(route('api.device.image.list'));
        const storeUrl = @json(route('api.device.image.store'));
        const updateUrl = @json(route('api.device.image.update'));
        const deleteUrl = @json(route('device.image.delete'));
    </script>
    <script src="assets/js/gallery/script.js?v={{ time() }}"></script>
    <script src="assets/js/paginate/script.js?v={{ time() }}"></script>
    <script src="assets/js/device/image/modals.js?v={{ time() }}"></script>
    <script src="assets/js/device/image/list.js?v={{ time() }}"></script>
@endsection
