@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Đề nghị/Phát hành', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('official-document.create')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="review-approve-modal" title="Xác nhận đồng ý kiểm tra" size="md" method="POST" nested="true">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Loại lĩnh vực
                </label>
                <select name="official_document_sector_id" class="form-control" required>
                    <x-select-options :items="$officialDocumentSectors" />
                </select>
            </div>
            <div class="my-1">
                <label>
                    File điều chỉnh
                    <small class="text-info">
                        (nếu cần điều chỉnh)
                    </small>
                </label>
                <input type="file" class="form-control" name="revision_docx_file" accept=".docx">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
    <x-modal id="review-reject-modal" title="Xác nhận từ chối kiểm tra" size="md" method="POST" nested="true">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Người kiểm tra
                    <small class="text-info">
                        (đề xuất người kiểm tra khác)
                    </small>
                </label>
                <select name="reviewed_by" class="form-control" required>
                    <x-select-options :items="$reviewers" />
                </select>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
    <x-approve-modal id="approve-modal" title="Xác nhận duyệt văn bản" size="md" method="post"
        noteName="approval_note">
        <x-slot:extraFields>
            <div class="my-1">
                <label>
                    File phê duyệt
                </label>
                <input type="file" class="form-control" name="approve_docx_file" accept=".docx" required>
            </div>
        </x-slot:extraFields>
    </x-approve-modal>
    <x-approve-modal id="reject-modal" title="Xác nhận từ chối văn bản" size="md" method="post"
        noteName="rejection_note" buttonVariant="danger">
        <x-slot:extraFields>
            <div class="my-1">
                <label>
                    File nhận xét
                </label>
                <input type="file" class="form-control" name="comment_docx_file" accept=".docx" required>
            </div>
        </x-slot:extraFields>
    </x-approve-modal>
    <x-modal id="release-modal" title="Xác nhận phát hành văn bản" size="md" method="POST" nested="true">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Thời gian phát hành
                </label>
                <input type="date" name="released_date" class="form-control" required />
            </div>
            <div class="my-1">
                <label>
                    Số và ký hiệu
                </label>
                <input type="text" name="document_number" class="form-control" required />
            </div>
            <div class="my-1">
                <label>
                    File phát hành
                </label>
                <input type="file" name="released_pdf_file" class="form-control" required accept=".pdf" />
            </div>
            <div class="my-1">
                <label>
                    Người kiểm tra
                    <small class="text-info">
                        (nếu có thay đổi)
                    </small>
                </label>
                <select name="signed_by" class="form-control">
                    <x-select-options :items="$users" />
                </select>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.official-document.list'));
        const editUrl = @json(route('official-document.edit'));
        const deleteUrl = @json(route('official-document.delete'));
        const reviewApproveUrl = @json(route('official-document.review-approve'));
        const reviewRejectUrl = @json(route('official-document.review-reject'));
        const approveUrl = @json(route('official-document.approve'));
        const rejectUrl = @json(route('official-document.reject'));
        const releaseUrl = @json(route('official-document.release'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/modals.js?v={{ time() }}"></script>
    <script src="assets/js/components/approve-reject-modal-event.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/list.js?v={{ time() }}"></script>
@endsection
